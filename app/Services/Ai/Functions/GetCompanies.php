<?php

namespace App\Services\Ai\Functions;

use App\Models\Company;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;

class GetCompanies
{
    /**
     * List all companies in the organisation.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @return array{count: int, companies: array}
     */
    #[WAFunction('get_companies', 'List all companies in the organisation', 'company.view')]
    public function execute(
        User $user,
    ): array {
        $companies = Company::orderBy('name')->get();

        return [
            'count' => $companies->count(),
            'companies' => $companies->map(fn (Company $c) => [
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'address' => $c->address,
                'status' => $c->status,
            ])->toArray(),
        ];
    }
}
