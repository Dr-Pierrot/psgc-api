<?php

namespace App\Http\Controllers\API\PSGC;

use App\Http\Controllers\Controller;
use App\Models\PSGC\Q12026;

class Q12026Controller extends Controller
{
    public function index()
    {
        $data = Q12026::paginate(10);
        return response()->json($data);
    }

    public function geographic_level()
    {
        $geographic_level = Q12026::where('geographic_level', '!=', 'NULL')->select('geographic_level')->distinct()->get();
        return response()->json($geographic_level);
    }

    public function city_classification()
    {
        $city_classification = Q12026::where('geographic_level', 'City')->select('city_classification')->distinct()->get();
        return response()->json($city_classification);
    }

    public function income_classification()
    {
        $income_classification = Q12026::where('geographic_level', 'City')->select('income_classification')->distinct()->get();
        return response()->json($income_classification);
    }

    public function regions()
    {
        $regions = Q12026::where('geographic_level', 'Reg')->get();
        return response()->json($regions);
    }

    public function region($psgc_code)
    {
        $regions = Q12026::where('geographic_level', 'Reg')->where('psgc_code', $psgc_code)->get();
        return response()->json($regions);
    }

    public function region_provinces($psgc_code)
    {
        $provinces = Q12026::where('geographic_level', 'Prov')->where('region_code', substr($psgc_code, 0, 2))->get();
        return response()->json($provinces);
    }

    public function region_cities($psgc_code)
    {
        $cities = Q12026::where('geographic_level', 'City')->where('region_code', substr($psgc_code, 0, 2))->get();
        return response()->json($cities);
    }

    public function region_municipalities($psgc_code)
    {
        $municipalities = Q12026::where('geographic_level', 'Mun')->where('region_code', substr($psgc_code, 0, 2))->get();
        return response()->json($municipalities);
    }

    public function region_submunicipalities($psgc_code)
    {
        $submunicipalities = Q12026::where('geographic_level', 'SubMun')->where('region_code', substr($psgc_code, 0, 2))->get();
        return response()->json($submunicipalities);
    }

    public function region_barangays($psgc_code)
    {
        $barangays = Q12026::where('geographic_level', 'Bgy')->where('region_code', substr($psgc_code, 0, 2))->get();
        return response()->json($barangays);
    }
}
