<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ProvisionSuperAdmin extends Command
{
    protected $signature = 'cms:provision-super-admin {email? : Existing or new Super Admin email address}';

    protected $description = 'Promote an existing user, or securely create a new user, as the initial Super Admin.';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email address');
        $email = is_string($email) ? strtolower(trim($email)) : '';

        $emailValidator = Validator::make(['email' => $email], [
            'email' => ['required', 'email'],
        ]);

        if ($emailValidator->fails()) {
            $this->error('A valid email address is required.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            if (! $this->confirm('No user exists for this email. Create one now?', true)) {
                $this->warn('No changes were made.');

                return self::SUCCESS;
            }

            $user = $this->createUser($email);
        }

        if (! $user) {
            return self::FAILURE;
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate(Rbac::ROLE_SUPER_ADMIN, Rbac::GUARD_WEB);

        $user->syncRoles([Rbac::ROLE_SUPER_ADMIN]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Super Admin provisioning completed.');

        return self::SUCCESS;
    }

    private function createUser(string $email): ?User
    {
        $name = $this->ask('Name');
        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return null;
        }

        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
