<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialMediaDownloader
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 300,
            'verify' => false,
        ]);
    }

    public function download(string $url, string $sourceType): array
    {
        switch ($sourceType) {
            case 'youtube':
                return $this->downloadFromYoutube($url);
            case 'facebook':
                return $this->downloadFromFacebook($url);
            case 'tiktok':
                return $this->downloadFromTikTok($url);
            default:
                throw new \Exception('Unsupported source type: ' . $sourceType);
        }
    }

    private function downloadFromYoutube(string $url): array
    {
        // Using yt-dlp (recommended) or youtube-dl
        $filename = 'youtube_' . Str::random(12) . '.%(ext)s';
        $outputPath = Storage::path('uploads/' . $filename);

        $command = [
            'yt-dlp',
            '--extract-flat',
            '--get-filename',
            '--output', $outputPath,
            $url
        ];

        $process = new \Symfony\Component\Process\Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception('Failed to download from YouTube: ' . $process->getErrorOutput());
        }

        // Get actual filename and download
        $actualFilename = trim($process->getOutput());

        $downloadCommand = [
            'yt-dlp',
            '--output', $outputPath,
            '--format', 'best[height<=720]/best',
            $url
        ];

        $downloadProcess = new \Symfony\Component\Process\Process($downloadCommand);
        $downloadProcess->setTimeout(1800); // 30 minutes
        $downloadProcess->run();

        if (!$downloadProcess->isSuccessful()) {
            throw new \Exception('Failed to download video: ' . $downloadProcess->getErrorOutput());
        }

        $fileInfo = pathinfo($actualFilename);

        return [
            'filename' => basename($actualFilename),
            'format' => $fileInfo['extension'] ?? 'mp4',
            'file_size' => filesize($actualFilename),
            'metadata' => [
                'source' => 'youtube',
                'original_url' => $url,
            ],
        ];
    }

    private function downloadFromFacebook(string $url): array
    {
        // Using yt-dlp for Facebook videos
        $filename = 'facebook_' . Str::random(12);
        $outputTemplate = Storage::path('uploads/' . $filename . '.%(ext)s');

        $command = [
            'yt-dlp',
            '--output', $outputTemplate,
            '--format', 'best',
            $url
        ];

        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(1800);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception('Failed to download from Facebook: ' . $process->getErrorOutput());
        }

        // Find the downloaded file
        $files = Storage::files('uploads');
        $downloadedFile = collect($files)->first(function ($file) use ($filename) {
            return str_contains($file, $filename);
        });

        if (!$downloadedFile) {
            throw new \Exception('Downloaded file not found');
        }

        $fileInfo = pathinfo($downloadedFile);

        return [
            'filename' => basename($downloadedFile),
            'format' => $fileInfo['extension'] ?? 'mp4',
            'file_size' => Storage::size($downloadedFile),
            'metadata' => [
                'source' => 'facebook',
                'original_url' => $url,
            ],
        ];
    }

    private function downloadFromTikTok(string $url): array
    {
        // Using yt-dlp for TikTok videos
        $filename = 'tiktok_' . Str::random(12);
        $outputTemplate = Storage::path('uploads/' . $filename . '.%(ext)s');

        $command = [
            'yt-dlp',
            '--output', $outputTemplate,
            '--format', 'best',
            $url
        ];

        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(1800);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception('Failed to download from TikTok: ' . $process->getErrorOutput());
        }

        // Find the downloaded file
        $files = Storage::files('uploads');
        $downloadedFile = collect($files)->first(function ($file) use ($filename) {
            return str_contains($file, $filename);
        });

        if (!$downloadedFile) {
            throw new \Exception('Downloaded file not found');
        }

        $fileInfo = pathinfo($downloadedFile);

        return [
            'filename' => basename($downloadedFile),
            'format' => $fileInfo['extension'] ?? 'mp4',
            'file_size' => Storage::size($downloadedFile),
            'metadata' => [
                'source' => 'tiktok',
                'original_url' => $url,
            ],
        ];
    }
}
