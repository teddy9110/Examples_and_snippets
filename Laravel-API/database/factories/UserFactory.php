<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Rhf\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
        //        'name' => $faker->name,
        'first_name' => $this->faker->firstName,
        'surname' => $this->faker->lastName,
        'email' => $this->faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
        ];
    }
}
