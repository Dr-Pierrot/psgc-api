<?php

namespace App\Http\Controllers\API\PSGC;

use App\Http\Controllers\Controller;
use App\Models\PSGC\Q12026;
use Illuminate\Http\Request;

class Q12026Controller extends Controller
{
    public function index()
    {
        $data = Q12026::paginate(10);
        return response()->json($data);
    }
}
