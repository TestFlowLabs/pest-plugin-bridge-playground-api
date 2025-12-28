<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ExternalApiController extends Controller
{
    /**
     * Get IP geolocation information from external API.
     *
     * This endpoint demonstrates external API integration that can be
     * mocked using Bridge::fake() in browser tests.
     */
    public function ipInfo(): JsonResponse
    {
        $response = Http::get('https://ipapi.co/json');

        if ($response->failed()) {
            return response()->json([
                'error' => 'Unable to fetch location',
            ], 503);
        }

        $data = $response->json();

        return response()->json([
            'country_name' => $data['country_name'] ?? 'Unknown',
            'city' => $data['city'] ?? 'Unknown',
            'region' => $data['region'] ?? 'Unknown',
            'ip' => $data['ip'] ?? 'Unknown',
        ]);
    }
}
