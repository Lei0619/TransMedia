<?php

namespace App\Jobs;

use App\Events\ConversionCompleted;
use App\Events\ConversionFailed;
use App\Events\ConversionStarted;
use App\Models\Conversion;
use App\Services\ConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessConversion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Conversion $conversion)
    {
    }

    public function handle(ConversionService $conversionService): void
    {
        try {
            // Update status and broadcast started event
            $this->conversion->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            event(new ConversionStarted($this->conversion));

            // Process the conversion
            $result = $conversionService->convert($this->conversion);

            // Update with completion data
            $this->conversion->update([
                'status' => 'completed',
                'converted_filename' => $result['filename'],
                'file_size' => $result['file_size'],
                'duration' => $result['duration'] ?? null,
                'metadata' => $result['metadata'] ?? null,
                'completed_at' => now(),
            ]);

            event(new ConversionCompleted($this->conversion));

        } catch (\Exception $e) {
            Log::error('Conversion failed', [
                'conversion_id' => $this->conversion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->conversion->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            event(new ConversionFailed($this->conversion));
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->conversion->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        event(new ConversionFailed($this->conversion));
    }
}
