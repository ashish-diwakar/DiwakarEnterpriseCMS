<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Rbac;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $summaryCards = [];

        if ($user?->can(Rbac::PERMISSION_USERS_VIEW)) {
            $summaryCards[] = [
                'label' => 'Users',
                'value' => User::query()->count(),
                'description' => 'Total CMS user accounts.',
                'href' => route('admin.users.index'),
                'actionLabel' => 'View Users',
            ];
        }

        if ($user?->can(Rbac::PERMISSION_ROLES_VIEW)) {
            $summaryCards[] = [
                'label' => 'Roles',
                'value' => Role::query()
                    ->where('guard_name', Rbac::GUARD_WEB)
                    ->count(),
                'description' => 'Configured CMS roles for the web guard.',
                'href' => route('admin.roles.index'),
                'actionLabel' => 'View Roles',
            ];

            $summaryCards[] = [
                'label' => 'Permissions',
                'value' => Permission::query()
                    ->where('guard_name', Rbac::GUARD_WEB)
                    ->count(),
                'description' => 'Configured CMS permissions for the web guard.',
                'href' => route('admin.roles.index'),
                'actionLabel' => 'View Roles',
            ];
        }

        return view('admin.dashboard.index', [
            'summaryCards' => $summaryCards,
        ]);
    }
}
