<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * gps_helper.php
 *
 * Helper berisi fungsi-fungsi utilitas GPS/Geolocation.
 * - get_distance($lat1, $lon1, $lat2, $lon2, $unit = 'meter')
 *   Mengembalikan jarak antara dua koordinat (latitude, longitude).
 *   Unit dapat 'meter' (default) atau 'kilometer'.
 *   Menggunakan rumus Haversine untuk akurasi.
 */

if ( ! function_exists('get_distance')) {
    /**
     * Hitung jarak menggunakan Haversine.
     *
     * @param float $lat1 Latitude titik pertama
     * @param float $lon1 Longitude titik pertama
     * @param float $lat2 Latitude titik kedua
     * @param float $lon2 Longitude titik kedua
     * @param string $unit 'meter' atau 'kilometer'
     * @return float Jarak dalam unit yang diminta
     */
    function get_distance($lat1, $lon1, $lat2, $lon2, $unit = 'meter')
    {
        // Konversi derajat ke radian
        $lat1_rad = deg2rad($lat1);
        $lon1_rad = deg2rad($lon1);
        $lat2_rad = deg2rad($lat2);
        $lon2_rad = deg2rad($lon2);

        $delta_lat = $lat2_rad - $lat1_rad;
        $delta_lon = $lon2_rad - $lon1_rad;

        $a = sin($delta_lat / 2) * sin($delta_lat / 2) +
            cos($lat1_rad) * cos($lat2_rad) *
            sin($delta_lon / 2) * sin($delta_lon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Radius bumi dalam meter
        $earth_radius = 6371000; // meter
        $distance = $earth_radius * $c; // dalam meter

        if ($unit === 'kilometer') {
            return $distance / 1000; // konversi ke km
        }
        // default meter
        return $distance;
    }
}
?>
