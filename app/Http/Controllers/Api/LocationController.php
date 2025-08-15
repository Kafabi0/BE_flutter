<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    /**
     * Ambil daftar lokasi Indonesia (dummy/static)
     */
    public function getIndonesiaLocations()
    {
        $locations = [
            ['name' => 'Jakarta'],
            ['name' => 'Bandung'],
            ['name' => 'Surabaya'],
            ['name' => 'Medan'],
            ['name' => 'Yogyakarta'],
            ['name' => 'Bali'],
            ['name' => 'Semarang'],
            ['name' => 'Makassar'],
            ['name' => 'Malang'],
            ['name' => 'Bogor'],
        ];

        return response()->json($locations);
    }

    /**
     * Reverse Geocoding menggunakan Nominatim (OpenStreetMap)
     */
    public function reverseGeocode(Request $request)
    {
        $latitude = $request->query('lat');
        $longitude = $request->query('lng');

        if (!$latitude || !$longitude) {
            return response()->json(['error' => 'Latitude & longitude dibutuhkan'], 400);
        }

        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&addressdetails=1";

        $response = Http::withHeaders([
            'User-Agent' => 'FlutterAppExample/1.0'
        ])->get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal memanggil Nominatim API'], 500);
        }

        $data = $response->json();
        $address = $data['display_name'] ?? 'Lokasi tidak ditemukan';

        return response()->json([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
        ]);
    }
}
