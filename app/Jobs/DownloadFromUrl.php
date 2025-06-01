<?php

namespace App\Jobs;

use App\Models\Conversion;
use App\Services\SocialMediaDownloader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadFromUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Conversion $conversion)
    {
    }

    public function handle(SocialMediaDownloader $downloader): void
    {
        try {
            $result = $downloader->download($this->conversion->source_url, $this->conversion->source_type);

            $this->conversion->update([
                'original_filename' => $result['filename'],
                'original_format' => $result['format'],
                'file_size' => $result['file_size'],
                'metadata' => $result['metadata'] ?? null,
            ]);

            // Queue the conversion job
            ProcessConversion::dispatch($this->conversion);

        } catch (\Exception $e) {
            Log::error('Download from URL failed', [
                'conversion_id' => $this->conversion->id,
                'url' => $this->conversion->source_url,
                'error' => $e->getMessage(),
            ]);

            $this->conversion->update([
                'status' => 'failed',
                'error_message' => 'Download failed: ' . $e->getMessage(),
            ]);
        }
    }
}
