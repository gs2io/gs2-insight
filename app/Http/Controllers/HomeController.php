<?php

namespace App\Http\Controllers;

use App\Domain\GcpDomain;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public static function index(Request $request): View|RedirectResponse
    {
        if ((new GcpDomain())->model() == null) {
            return redirect('/gcp');
        }
        return view('index');
    }
}
