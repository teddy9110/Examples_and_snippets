<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Rhf\Modules\User\Models\UserRole;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createGodPermissions();
        $this->createHigherPermissions();
        $this->createAdminPermissions();
        $this->createCustomerPermissions();
    }

    /**
     *
     */
    private function createGodPermissions()
    {
        $permissions = [
            'view:facebook-content' => true,
            'add:facebook-content' => true,
            'delete:facebook-content' => true,
            'list:content' => true,
            'add:content' => true,
            'view:content' => true,
            'update:content' => true,
            'delete:content' => true,
            'list:categories' => true,
            'add:category' => true,
            'view:category' => true,
            'update:category' => true,
            'delete:category' => true,
            'list:users' => true,
            'add:user' => true,
            'view:user' => true,
            'update:user' => true,
            'delete:user' => true,
            'purge:user' => true,
            'restore:user' => true,
            'unlink-mfp:user' => true,
            'update:user-password' => true,
            'update:user-permission' => true,
            'view:user-progress-pictures' => true,
            'delete:user-progress-picture' => true,
            'list:recipes' => true,
            'add:recipe' => true,
            'view:recipe' => true,
            'delete:recipe' => true,
            'update:recipe' => true,
            'view:services' => true,
            'update:service' => true,
            'view:promoted-products' => true,
            'add:promoted-product' => true,
            'update:promoted-product' => true,
            'delete:promoted-product' => true,
            'view:dashboard' => true,
            'admin:exercises' => true,
            'admin:workouts' => true,
            'view:exercise-preferences' => true,
            'view:notification' => true,
            'update:notification' => true,
            'send:notification' => true,
            'delete:notification' => true,
            'view:topic' => true,
            'update:topic' => true,
            'delete:topic' => true,
            'update:tags' => true,
            'list:direct-debit-signups' => true,
            'create:direct-debit-signups' => true,
            'view:transformations' => true,
            'delete:transformations' => true,
            'manage:subscriptions' => true,
            'manage:direct-debits' => true,
            'read:features' => true,
            'manage:features' => true,
            'view:videos' => true,
            'manage:videos' => true,
            'view:competitions' => true,
            'manage:competitions' => true,
        ];

        $god = UserRole::firstOrCreate([
           'name' => 'God',
           'slug' => 'god'
        ], ['permissions' => $permissions]);

        $this->havePermissionsChanged($god, $permissions);
    }

    private function createHigherPermissions()
    {
        $permissions = [
            'list:users' => true,
            'add:user' => true,
            'view:user' => true,
            'update:user' => true,
            'delete:user' => true,
            'purge:user' => true,
            'restore:user' => true,
            'unlink-mfp:user' => true,
            'update:user-password' => true,
            'update:user-permission' => true,
            'view:user-progress-pictures' => true,
            'delete:user-progress-picture' => true,
            'list:recipes' => true,
            'add:recipe' => true,
            'view:recipe' => true,
            'delete:recipe' => true,
            'update:recipe' => true,
            'view:exercise-preferences' => true,
            'list:direct-debit-signups' => true,
            'create:direct-debit-signups' => true,
            'manage:subscriptions' => true,
            'read:features' => true,
            'write:features' => true,
            'manage:direct-debits' => true
        ];

        $higher = UserRole::firstOrCreate([
            'name' => 'Higher Admin',
            'slug' => 'higher-admin'
        ], ['permissions' => $permissions]);

        $this->havePermissionsChanged($higher, $permissions);
    }

    private function createAdminPermissions()
    {
        $permissions = [
            'list:users' => true,
            'view:user' => true,
            'view:user-progress-pictures' => true,
            'delete:user-progress-picture' => true,
            'list:recipes' => true,
            'view:recipe' => true,
            'view:exercise-preferences' => true,
        ];

        $admin = UserRole::firstOrCreate([
            'name' => 'Admin',
            'slug' => 'admin',
        ], ['permissions' => $permissions]);

        $this->havePermissionsChanged($admin, $permissions);
    }

    private function createCustomerPermissions()
    {
        $permissions = [];

        $customer = UserRole::firstOrCreate([
            'name' => 'Customer',
            'slug' => 'customer',
        ], ['permissions' => $permissions]);

        $this->havePermissionsChanged($customer, $permissions);
    }

    /**
     * @param $query
     * @param array $permissions
     */
    private function havePermissionsChanged($query, array $permissions): void
    {
        $equal = $this->arrayEqual($query->permissions, $permissions);
        if ($equal !== true) {
            $this->command->line($query->name . ' has changes, updating permissions...');
            $query->update([
                'permissions' => $permissions
            ]);
            $query->save();
        }
    }

    /**
     * Compares that two arrays are equal
     *
     * @param $array1
     * @param $array2
     * @return bool
     */
    private function arrayEqual($array1, $array2)
    {
        $array1 = Arr::wrap($array1);
        $array2 = Arr::wrap($array2);

        if (count($array1) != count($array2)) {
            return false;
        }

        $keys = array_keys($array1);

        // Compare permission array keys
        $keyDiff = array_diff($keys, array_keys($array2));
        if (count($keyDiff)) {
            return false;
        }

        // Compare permission array values
        foreach ($keys as $key) {
            if ($array1[$key] != $array2[$key]) {
                return false;
            }
        }

        return true;
    }
}
