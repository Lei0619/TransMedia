<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversionRequest;
use App\Jobs\DownloadFromUrl;
use App\Jobs\ProcessConversion;
use App\Models\Conversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class ConversionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index()
    {
        $conversions = auth()->check()
            ? auth()->user()->conversions()->latest()->paginate(10)
            : collect();

        return view('conversions.index', compact('conversions'));
    }

    public function store(ConversionRequest $request): JsonResponse
    {
        $conversion = new Conversion([
            'user_id' => auth()->id(),
            'target_format' => $request->target_format,
            'status' => 'pending',
        ]);

        if ($request->hasFile('file')) {
            // Handle file upload
            $file = $request->file('file');
            $filename = Str::random(12) . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads', $filename);

            $conversion->fill([
                'original_filename' => $filename,
                'original_format' => $file->getClientOriginalExtension(),
                'source_type' => 'upload',
                'file_size' => $file->getSize(),
            ]);

            $conversion->save();
            ProcessConversion::dispatch($conversion);

        } elseif ($request->url) {
            // Handle URL download
            $sourceType = $this->detectSourceType($request->url);

            $conversion->fill([
                'source_url' => $request->url,
                'source_type' => $sourceType,
                'original_format' => 'unknown',
            ]);

            $conversion->save();
            DownloadFromUrl::dispatch($conversion);
        }

        return response()->json([
            'success' => true,
            'conversion_id' => $conversion->id,
            'message' => 'Conversion queued successfully!',
        ]);
    }

    public function show(Conversion $conversion): JsonResponse
    {
        $this->authorize('view', $conversion);

        return response()->json([
            'conversion' => $conversion->load('downloadHistory'),
        ]);
    }

    public function download(Conversion $conversion)
    {
        $this->authorize('download', $conversion);

        if ($conversion->status !== 'completed') {
            abort(404, 'File not ready for download');
        }

        $filePath = Storage::path('converted/' . $conversion->converted_filename);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $conversion->converted_filename);
    }

    public function destroy(Conversion $conversion): JsonResponse
    {
        $this->authorize('delete', $conversion);

        // Delete files
        if ($conversion->original_filename) {
            Storage::delete('uploads/' . $conversion->original_filename);
        }
        if ($conversion->converted_filename) {
            Storage::delete('converted/' . $conversion->converted_filename);
        }

        $conversion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversion deleted successfully!',
        ]);
    }

    private function detectSourceType(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        } elseif (str_contains($url, 'facebook.com') || str_contains($url, 'fb.watch')) {
            return 'facebook';
        } elseif (str_contains($url, 'tiktok.com')) {
            return 'tiktok';
        }

        return 'unknown';
    }
}
