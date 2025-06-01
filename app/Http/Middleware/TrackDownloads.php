<?php

namespace App\Http\Middleware;

use App\Models\Conversion; // Added to hint the type of the route parameter
use App\Models\DownloadHistory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Added for explicit Auth facade usage (optional, but good practice)

class TrackDownloads
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track successful downloads
        // Ensure the status code is 200 (OK) and 'conversion' route parameter exists
        if ($response->getStatusCode() === 200 && $request->route('conversion')) {

            /** @var \App\Models\User|null $user */
            $user = Auth::user(); // Explicitly get the user object

            // Retrieve the Conversion model instance from the route
            /** @var Conversion $conversion */
            $conversion = $request->route('conversion');

            DownloadHistory::create([
                'user_id' => $user ? $user->id : null, // Safely get user ID, null if not authenticated
                'conversion_id' => $conversion->id, // Get the ID from the Conversion model instance
                'downloaded_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}