<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Set the admins
        $admins = [
            [
                'first_name' => 'Matt',
                'surname' => 'Wade',
                'email' => 'mattwade@darwincreative.co.uk',
            ],
            [
                'first_name' => 'Stefanie',
                'surname' => 'Jones',
                'email' => 'Stephanie.jones@teamrhfitness.com',
            ],
            [
                'first_name' => 'Stephanie',
                'surname' => 'Gallagher',
                'email' => 'Stephanie.gallagher@teamrhfitness.com',
            ],
            [
                'first_name' => 'Liam',
                'surname' => 'Sharpe',
                'email' => 'Liam.sharpe@teamrhfitness.com',
            ],
            [
                'first_name' => 'Lucy',
                'surname' => 'Child',
                'email' => 'Lucy.child@teamrhfitness.com',
            ],
            [
                'first_name' => 'Natalie',
                'surname' => 'Sharpe',
                'email' => 'Natalie.sharpe@teamrhfitness.com',
            ],
            [
                'first_name' => 'Jodie',
                'surname' => 'Wyles',
                'email' => 'Jodie.wyles@teamrhfitness.com',
            ],
            [
                'first_name' => 'Peter',
                'surname' => 'Preece',
                'email' => 'Peter.preece@teamrhfitness.com',
            ],
            [
                'first_name' => 'Richie',
                'surname' => 'Barker',
                'email' => 'Richard.barker@teamrhfitness.com',
            ],
            [
                'first_name' => 'Charmayne',
                'surname' => 'Tinmurth',
                'email' => 'Charmayne.tinmurth@teamrhfitness.com',
            ],
            [
                'first_name' => 'Richie',
                'surname' => 'Howey',
                'email' => 'Richard.howey@teamrhfitness.com',
            ],
            [
                'first_name' => 'Rachael',
                'surname' => 'Hepton',
                'email' => 'Rachael.hepton@teamrhfitness.com',
            ],
        ];

        // Insert
        foreach ($admins as $admin) {
            DB::table('users')->insert([
                'admin' => 1,
                'first_name' => $admin['first_name'],
                'surname' => $admin['surname'],
                'email' => $admin['email'],
                'password' => bcrypt('dcrjpzfv'),
            ]);
        }
    }
}
