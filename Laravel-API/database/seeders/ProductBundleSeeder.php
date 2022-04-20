<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Product\Models\ProductBundles;
use Rhf\Modules\Product\Models\WorkoutProductBundleType;

class ProductBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        //cannot workout
        ProductBundles::create(
            [
                'title' => 'Zero',
                'bundle' => [
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0OTEzODYzNTU3NzQ=", //Vanilla Protein
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMjI1NTM0ODcwMzI5NA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0NTI1MTE2NDU3NTg=", //scales
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTU5NzQ3MzQzMTYxNA==",
                        "quantity" => 1
                    ],
                ],
                'introduction_text' => "Before you get started...",
                'closing_text' => "This is a little bit cheeky, but based on your answers we've put together a few items you might need to smash the plan. We don't take the piss, we keep out products as affordable as possible and they're all incredible quality.",
            ]
        );

        //gym bundle
        ProductBundles::create(
            [
                'title' => 'Gym',
                'bundle' => [
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0OTEzODYzNTU3NzQ=", //Vanilla Protein
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMjI1NTM0ODcwMzI5NA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0NTI1MTE2NDU3NTg=", //scales
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTU5NzQ3MzQzMTYxNA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0NTg1NjI2ODI5NDI=", //booty bands
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTYxNzc3MjAyNzk2Ng==",
                        "quantity" => 1
                    ],
                ],
                'introduction_text' => "Before you get started...",
                'closing_text' => "This is a little bit cheeky, but based on your answers we've put together a few items you might need to smash the plan. We don't take the piss, we keep out products as affordable as possible and they're all incredible quality.",
            ]
        );

        //home bundle
        ProductBundles::create(
            [
                'title' => 'Home',
                'bundle' => [
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0OTEzODYzNTU3NzQ=", //Vanilla Protein
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMjI1NTM0ODcwMzI5NA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0NTI1MTE2NDU3NTg=", //scales
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTU5NzQ3MzQzMTYxNA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ3MDg4NjQ5NTAzMzQ=",
                        //water bottle dumbbell
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMjM5NDI3NzMyMjgxNA==",
                        //SKU 10000034 | Single Bottle
                        "quantity" => 1
                    ],
                ],
                'introduction_text' => "Before you get started...",
                'closing_text' => "This is a little bit cheeky, but based on your answers we've put together a few items you might need to smash the plan. We don't take the piss, we keep out products as affordable as possible and they're all incredible quality.",
            ]
        );

        //grhaft bundle
        ProductBundles::create(
            [
                'title' => 'Grhaft',
                'bundle' => [
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0OTEzODYzNTU3NzQ=", //Vanilla Protein
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMjI1NTM0ODcwMzI5NA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ0NTI1MTE2NDU3NTg=", //scales
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTU5NzQ3MzQzMTYxNA==",
                        "quantity" => 1
                    ],
                    [
                        "product" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0LzQ3NjY2MDcxMTQzMDI=",
                        //20kg Barbell split Dumbbell
                        "variant" => "Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMjYxMDE2NzUyMTM0Mg==",
                        //No variant in list but testing from APPS,
                        "quantity" => 1
                    ],
                ],
                'introduction_text' => "Before you get started...",
                'closing_text' => "This is a little bit cheeky, but based on your answers we've put together a few items you might need to smash the plan. We don't take the piss, we keep out products as affordable as possible and they're all incredible quality.",
            ]
        );

        //zero | cannot workout
        WorkoutProductBundleType::create(
            [
                'exercise_frequency_id' => 1,
                'exercise_location_id' => null,
                'exercise_level_id' => null,
                'bundle_id' => 1
            ]
        );

        //home
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 2,
                'bundle_id' => 3
            ]
        );

        //Home | Athletic (gRHaft)
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 2,
                'exercise_level_id' => 1,
                'bundle_id' => 4
            ]
        );

        //Home | Athletic | 3 (gRHaft)
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 2,
                'exercise_level_id' => 1,
                'exercise_frequency_id' => 2,
                'bundle_id' => 4
            ]
        );

        //Home | Athletic | 6 (gRHaft)
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 2,
                'exercise_level_id' => 1,
                'exercise_frequency_id' => 3,
                'bundle_id' => 4
            ]
        );

        //home | Standard | 3
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 2,
                'exercise_level_id' => 2,
                'exercise_frequency_id' => 2,
                'bundle_id' => 3
            ]
        );

        //home | Standard | 6
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 2,
                'exercise_level_id' => 2,
                'exercise_frequency_id' => 3,
                'bundle_id' => 3
            ]
        );

        //Gym
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 1,
                'bundle_id' => 2
            ]
        );

        //Gym / Standard / 3
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 1,
                'exercise_level_id' => 2,
                'exercise_frequency_id' => 2,
                'bundle_id' => 2
            ]
        );

        //Gym / Standard / 6
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 1,
                'exercise_level_id' => 2,
                'exercise_frequency_id' => 3,
                'bundle_id' => 2
            ]
        );

        //Gym / Athletic / 3
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 1,
                'exercise_level_id' => 1,
                'exercise_frequency_id' => 2,
                'bundle_id' => 2
            ]
        );

        //Gym / Athletic / 6
        WorkoutProductBundleType::create(
            [
                'exercise_location_id' => 1,
                'exercise_level_id' => 1,
                'exercise_frequency_id' => 3,
                'bundle_id' => 2
            ]
        );
    }
}
