<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessConversion;
use App\Http\Requests\ConversionRequest;
use App\Jobs\DownloadFromUrl;
use App\Models\Conversion;
use App\Models\User; // <-- Added this import
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversionController extends Controller
{
    public function __construct()
    {
        // Apply 'auth' middleware to all methods except 'index'.
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Display a listing of the conversions for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Use type hinting for Auth::user() to help Intelephense.
        // It can still be null if not authenticated, hence the conditional check.
        /** @var User|null $user */
        $user = Auth::user();

        // Get conversions for the authenticated user, or an empty collection if not logged in.
        $conversions = $user
            ? $user->conversions()->latest()->paginate(10)
            : collect(); // Return an empty collection for guests.

        return view('conversions.index', compact('conversions'));
    }

    public function showFacebookConversions()
{
    $user = Auth::user();
    $conversions = $user ? $user->conversions()->where('source_type', 'facebook')->latest()->paginate(10) : collect();

    return view('facebook', ['conversions' => $conversions]);
}

public function showTikTokConversions()
{
    $user = Auth::user();
    $conversions = $user ? $user->conversions()->where('source_type', 'tiktok')->latest()->paginate(10) : collect();

    return view('tiktok', ['conversions' => $conversions]);
}

public function showYouTubeConversions()
{
    $user = Auth::user();
    $conversions = $user ? $user->conversions()->where('source_type', 'youtube')->latest()->paginate(10) : collect();

    return view('youtube', ['conversions' => $conversions]);
}

    /**
     * Store a newly created conversion in storage.
     *
     * @param ConversionRequest $request
     * @return JsonResponse
     */
    public function store(ConversionRequest $request): JsonResponse
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user(); // Get the authenticated user or null.

        $conversion = new Conversion([
            'user_id' => optional($currentUser)->id, // Use optional() for safety if user is null.
            'target_format' => $request->target_format,
            'status' => 'pending',
        ]);

        if ($request->hasFile('file')) {
            // Handle file upload
            $file = $request->file('file');
            // Ensure a unique filename to prevent clashes.
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension(); // More random string for filenames.
            $file->storeAs('uploads', $filename);

            $conversion->fill([
                'original_filename' => $filename,
                'original_format' => $file->getClientOriginalExtension(),
                'source_type' => 'upload',
                'file_size' => $file->getSize(),
            ]);

            $conversion->save();
            // Dispatch job for processing the uploaded file.
            ProcessConversion::dispatch($conversion);

        } elseif ($request->url) {
            // Handle URL download
            $sourceType = $this->detectSourceType($request->url);

            $conversion->fill([
                'source_url' => $request->url,
                'source_type' => $sourceType,
                'original_format' => pathinfo($request->url, PATHINFO_EXTENSION) ?: 'unknown', // Try to get extension from URL.
            ]);

            $conversion->save();
            // Dispatch job for downloading from URL.
            DownloadFromUrl::dispatch($conversion);
        } else {
            // If neither file nor URL is provided, return an error.
            return response()->json([
                'success' => false,
                'message' => 'No file or URL provided for conversion.',
            ], 400); // Bad Request
        }

        return response()->json([
            'success' => true,
            'conversion_id' => $conversion->id,
            'message' => 'Conversion queued successfully!',
        ]);
    }

    /**
     * Display the specified conversion.
     *
     * @param Conversion $conversion
     * @return JsonResponse
     */
    public function show(Conversion $conversion): JsonResponse
    {
        // Use Laravel's authorization gate/policy.
        $this->authorize('view', $conversion);

        return response()->json([
            'conversion' => $conversion->load('downloadHistory'), // Eager load download history.
        ]);
    }

    /**
     * Download the converted file.
     *
     * @param Conversion $conversion
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function download(Conversion $conversion)
    {
        // Use Laravel's authorization gate/policy.
        $this->authorize('download', $conversion);

        if ($conversion->status !== 'completed') {
            abort(404, 'File not ready for download.');
        }

        // Ensure the path is correct for Storage.
        // Storage::disk('local')->path() assumes default local disk.
        $filePath = Storage::disk('local')->path('converted/' . $conversion->converted_filename);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        // Use response()->download for proper headers and file handling.
        return response()->download($filePath, $conversion->converted_filename);
    }

    /**
     * Remove the specified conversion from storage.
     *
     * @param Conversion $conversion
     * @return JsonResponse
     */
    public function destroy(Conversion $conversion): JsonResponse
    {
        // Use Laravel's authorization gate/policy.
        $this->authorize('delete', $conversion);

        // Delete original and converted files if they exist.
        if ($conversion->original_filename && Storage::exists('uploads/' . $conversion->original_filename)) {
            Storage::delete('uploads/' . $conversion->original_filename);
        }
        if ($conversion->converted_filename && Storage::exists('converted/' . $conversion->converted_filename)) {
            Storage::delete('converted/' . $conversion->converted_filename);
        }

        $conversion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversion deleted successfully!',
        ]);
    }

    /**
     * Detects the source type of a given URL.
     *
     * @param string $url
     * @return string
     */
    private function detectSourceType(string $url): string
    {
        // Use Str::contains for cleaner checks.
        if (Str::contains($url, ['googleusercontent.com/youtube.com/1', 'googleusercontent.com/youtube.com/2', 'youtube.com'])) {
            return 'youtube';
        } elseif (Str::contains($url, ['facebook.com', 'fb.watch'])) {
            return 'facebook';
        } elseif (Str::contains($url, 'tiktok.com')) {
            return 'tiktok';
        }

        return 'unknown';
    }
}
