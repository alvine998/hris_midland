<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Package;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        $packages = Package::active()->latest()->get();
        $faqs = Faq::active()->ordered()->get();

        return view('landing', compact('packages', 'faqs'));
    }
}
