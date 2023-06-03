<?php

namespace App\Http\Controllers\Api;

use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GeoRequest;
use App\Services\GeolocationService;

class GeolocationController extends Controller
{

    public function __construct(protected GeolocationService $geolocationService)
    {
    }
    public function nearbyPlaces(GeoRequest $request)
    {
        try {
            $lat = $request->lat;
            $lng = $request->long;
            $radius = $request->radius ?? 5000;
            $results = $this->geolocationService->getNearbyPlace($lat, $lng, $radius);
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

    public function placeDetails(GeoRequest $request)
    {
        try {
            $lat = $request->lat;
            $lng = $request->long;
            $result = $this->geolocationService->getPlaceDetails($lat, $lng);
            $result = [
                'name' => $result->name ?? '',
                'formatted_address' => $result->formatted_address ?? '',
                'formatted_phone_number' => $result->formatted_phone_number ?? '',
                'photos' => $result->photos ?? [],
                'location' => [
                    'lat' => $result?->geometry?->location?->lat,
                    'lng' => $result?->geometry?->location?->lng,
                ],
                'icon' => $result?->icon,
                'place_id' => $result?->place_id,
                'reference' => $result?->reference,
                'types' => $result?->types,
            ];


            return response()->apiSuccess('Place Details', $result);
        } catch (Throwable $e) {
            return response()->apiError($e->getMessage(), $e->getCode());
        }
    }

    public function placeDetailsByPlaceId(Request $request)
    {
        $request->validate([
            'place_id' => 'required|string',
        ]);
        try {
            $result = $this->geolocationService->getPlaceDetailsByPlaceId($request->place_id);
            $result = [
                'name' => $result->name ?? '',
                'formatted_address' => $result->formatted_address ?? '',
                'formatted_phone_number' => $result->formatted_phone_number ?? '',
                'photos' => $result->photos ?? [],
                'location' => [
                    'lat' => $result?->geometry?->location?->lat,
                    'lng' => $result?->geometry?->location?->lng,
                ],
                'icon' => $result?->icon,
                'place_id' => $result?->place_id,
                'reference' => $result?->reference,
                'types' => $result?->types,
            ];
            return response()->apiSuccess('Place Details', $result);
        } catch (Throwable $e) {
            return response()->apiError($e->getMessage(), $e->getCode());
        }
    }
}
