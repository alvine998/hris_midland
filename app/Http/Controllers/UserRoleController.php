<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function index(Request $request): View
    {
        $userRoles = ListSearchService::apply(UserRole::with(['user', 'role']), $request, [], [
            'user' => ['name', 'email'],
            'role' => ['name'],
        ])->paginate(10)->withQueryString();

        return view('user-roles.index', [
            'userRoles' => $userRoles,
            'users' => User::all(),
            'roles' => Role::all(),
        ]);
    }

    public function store(StoreUserRoleRequest $request): RedirectResponse
    {
        UserRole::create($request->validated());

        return redirect()->route('user-roles.index')->with('success', 'User role assigned successfully.');
    }

    public function update(StoreUserRoleRequest $request, UserRole $userRole): RedirectResponse
    {
        $userRole->update($request->validated());

        return redirect()->route('user-roles.index')->with('success', 'User role updated successfully.');
    }

    public function destroy(UserRole $userRole): RedirectResponse
    {
        $userRole->delete();

        return redirect()->route('user-roles.index')->with('success', 'User role removed successfully.');
    }
}
