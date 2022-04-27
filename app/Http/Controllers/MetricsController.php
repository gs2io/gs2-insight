<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MetricsController extends Controller
{
    public static function index(Request $request): View
    {
        return view('metrics');
    }
}
