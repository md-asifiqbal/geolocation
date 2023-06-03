<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GeolocationController extends Controller
{
    public function nearbyPlaces(Request $request)
    {
        try {
            $lat = $request->lat;
            $lng = $request->long;
            $radius = $request->radius ?? 5000;
            $response = $this->callApi($lat, $lng, $radius);
            $results = collect(json_decode($response)->results);
            $results = $results->map(function ($item) {
                return [
                    'name' => $item->name ?? '',
                    'vicinity' => $item->vicinity ?? '',
                    'location' => [
                        'lat' => $item?->geometry?->location?->lat,
                        'lng' => $item?->geometry?->location?->lng,
                    ],
                    'icon' => $item?->icon,
                    'place_id' => $item?->place_id,
                    'reference' => $item?->reference,
                    'types' => $item?->types,
                ];
            });
            return response()->apiSuccess('Nearby Places', $results);
        } catch (Throwable $e) {
            return response()->apiError($e->getMessage(), $e->getCode());
        }
    }

    public function callApi($lat, $lng, $radius)
    {
        $key = env('GOOGLE_MAP_API_KEY', 'AIzaSyBxdpoBaLfWkQhHsaVdZt8zOGJ_58GWmiU');
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$lat,$lng&radius=$radius&key=$key";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return the result, do not print
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
