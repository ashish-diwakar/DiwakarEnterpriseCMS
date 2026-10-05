<?php

namespace App\Support;

final class Rbac
{
    public const GUARD_WEB = 'web';

    public const ROLE_SUPER_ADMIN = 'Super Admin';

    public const ROLE_ADMIN = 'Admin';

    public const PERMISSION_ADMIN_ACCESS = 'admin.access';

    public const PERMISSION_USERS_VIEW = 'users.view';

    public const PERMISSION_USERS_ASSIGN_ROLES = 'users.assign_roles';

    public const PERMISSION_ROLES_VIEW = 'roles.view';

    public const PERMISSION_ROLES_MANAGE_PERMISSIONS = 'roles.manage_permissions';

    /**
     * @return list<string>
     */
    public static function permissions(): array
    {
        return [
            self::PERMISSION_ADMIN_ACCESS,
            self::PERMISSION_USERS_VIEW,
            self::PERMISSION_USERS_ASSIGN_ROLES,
            self::PERMISSION_ROLES_VIEW,
            self::PERMISSION_ROLES_MANAGE_PERMISSIONS,
        ];
    }

    /**
     * @return list<string>
     */
    public static function adminPermissions(): array
    {
        return [
            self::PERMISSION_ADMIN_ACCESS,
            self::PERMISSION_USERS_VIEW,
            self::PERMISSION_ROLES_VIEW,
        ];
    }
}
