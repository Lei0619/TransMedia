<?php

namespace App\Services;

use App\Models\Conversion;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ConversionService
{
    public function convert(Conversion $conversion): array
    {
        $inputPath = Storage::path('uploads/' . $conversion->original_filename);
        $outputFilename = pathinfo($conversion->original_filename, PATHINFO_FILENAME) . '.' . $conversion->target_format;
        $outputPath = Storage::path('converted/' . $outputFilename);

        // Ensure directories exist
        Storage::makeDirectory('converted');

        // Build FFmpeg command based on target format
        $command = $this->buildFfmpegCommand($inputPath, $outputPath, $conversion->target_format);

        $process = new Process($command);
        $process->setTimeout(3600); // 1 hour timeout
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Get file info
        $fileSize = filesize($outputPath);
        $metadata = $this->getMediaMetadata($outputPath);

        return [
            'filename' => $outputFilename,
            'file_size' => $fileSize,
            'duration' => $metadata['duration'] ?? null,
            'metadata' => $metadata,
        ];
    }

    private function buildFfmpegCommand(string $input, string $output, string $format): array
    {
        $ffmpegPath = config('app.ffmpeg_path', 'ffmpeg');

        $baseCommand = [$ffmpegPath, '-i', $input];

        switch (strtolower($format)) {
            case 'mp3':
                return array_merge($baseCommand, [
                    '-codec:a', 'libmp3lame',
                    '-b:a', '192k',
                    '-y',
                    $output
                ]);

            case 'mp4':
                return array_merge($baseCommand, [
                    '-codec:v', 'libx264',
                    '-codec:a', 'aac',
                    '-crf', '23',
                    '-preset', 'medium',
                    '-y',
                    $output
                ]);

            case 'wav':
                return array_merge($baseCommand, [
                    '-codec:a', 'pcm_s16le',
                    '-y',
                    $output
                ]);

            case 'avi':
                return array_merge($baseCommand, [
                    '-codec:v', 'libx264',
                    '-codec:a', 'libmp3lame',
                    '-y',
                    $output
                ]);

            default:
                return array_merge($baseCommand, ['-y', $output]);
        }
    }

    private function getMediaMetadata(string $filePath): array
    {
        $ffprobePath = config('app.ffprobe_path', 'ffprobe');

        $command = [
            $ffprobePath,
            '-v', 'quiet',
            '-print_format', 'json',
            '-show_format',
            '-show_streams',
            $filePath
        ];

        $process = new Process($command);
        $process->run();

        if ($process->isSuccessful()) {
            $output = json_decode($process->getOutput(), true);

            return [
                'duration' => (int) ($output['format']['duration'] ?? 0),
                'bitrate' => $output['format']['bit_rate'] ?? null,
                'streams' => $output['streams'] ?? [],
            ];
        }

        return [];
    }
}