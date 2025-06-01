<?php

namespace App\Http\Middleware;

use App\Models\DownloadHistory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDownloads
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track successful downloads
        if ($response->getStatusCode() === 200 && $request->route('conversion')) {
            DownloadHistory::create([
                'user_id' => auth()->id(),
                'conversion_id' => $request->route('conversion'),
                'downloaded_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}