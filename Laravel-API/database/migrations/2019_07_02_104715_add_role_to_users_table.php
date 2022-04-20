<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserRole;

class AddRoleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Grab the customer role from the UserRole table
            $customerRole = UserRole::where('slug', '=', 'customer')->first();

            // Add a new column called role to the users table
            $table->unsignedInteger('role_id')
                // Set the default value of this column to customer role ID
                ->default($customerRole->id)
                ->after('id');
        });

        // Get all current admin users in order to set their role correctly
        $users = User::where('admin', '=', 1)->get();
        $adminRole = UserRole::where('slug', '=', 'admin')->first();

        foreach ($users as $user) {
            $user->role_id = $adminRole->id;
            $user->save();
        }

        // After setting the role column correctly based off of admin column, drop the column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('admin')
                ->default(0)
                ->after('id');
        });


        // Set back all users who are god and admin role back to admin
        $adminGodRoles = UserRole::whereIn('slug', ['admin', 'higher-admin', 'god'])->pluck('id')->toArray();
        $users = User::whereIn('role_id', $adminGodRoles)->get();

        foreach ($users as $user) {
            $user->admin = 1;
            $user->save();
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
        });
    }
}
