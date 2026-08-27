<?php

namespace App\Http\Controllers;

use App\Services\SuperAdminOverviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminOverviewController extends Controller
{
    public function index(Request $request, SuperAdminOverviewService $service): View
    {
        abort_unless($request->user()?->isAdmin(), 403, 'Unauthorized action.');

        $divisionId = $request->integer('division_id') ?: null;

        return view('super-admin.overview', [
            ...$service->data($divisionId),
            'user' => $request->user(),
        ]);
    }
}
