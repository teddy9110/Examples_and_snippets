<?php

namespace Tests\Feature\App\Console\Commands;

use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Console\Commands\MigrateIntendedStartYearsCommand;
use App\Models\UserProfile;
use App\Models\UserProfileQualification;
use Mockery;

class MigrateIntendedStartYearsCommandTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Make sure a future row for undergraduate and postgraduate qualification are created
     */
    public function testCreatesQualificationRows()
    {
        UserProfile::factory()->count(1)->create(['user_id' => 1, 'intended_university_start_year' => 2024, 'intended_postgraduate_start_year' => null]);
        UserProfile::factory()->count(1)->create(['user_id' => 2, 'intended_university_start_year' => null, 'intended_postgraduate_start_year' => 2025]);
        UserProfile::factory()->count(1)->create(['user_id' => 3, 'intended_university_start_year' => 2026, 'intended_postgraduate_start_year' => 2027]);
        UserProfile::factory()->count(1)->create(['user_id' => 4, 'intended_university_start_year' => null, 'intended_postgraduate_start_year' => null]);

        Artisan::call('migrate:intended-start-years');

        self::assertEquals(UserProfileQualification::where('user_id', 1)->where('start_year', 2024)->where('qualification_id', 28)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 2)->where('start_year', 2025)->where('qualification_id', 29)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 3)->where('start_year', 2026)->where('qualification_id', 28)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 3)->where('start_year', 2027)->where('qualification_id', 29)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 4)->count(), 0);
    }

    /**
     * Make sure rows for undergraduate and postgraduate qualification are updated
     */
    public function testUpdatesQualificationRows()
    {
        UserProfile::factory()->count(1)->create(['user_id' => 1, 'intended_university_start_year' => 2024, 'intended_postgraduate_start_year' => null]);
        UserProfileQualification::factory()->count(1)->create(['user_id' => 1, 'qualification_id' => 28, 'future' => 1, 'start_year' => 2023]);
        UserProfile::factory()->count(1)->create(['user_id' => 2, 'intended_university_start_year' => null, 'intended_postgraduate_start_year' => 2025]);
        UserProfileQualification::factory()->count(1)->create(['user_id' => 2, 'qualification_id' => 29, 'future' => 1, 'start_year' => 2024]);
        UserProfile::factory()->count(1)->create(['user_id' => 3, 'intended_university_start_year' => 2026, 'intended_postgraduate_start_year' => 2027]);
        UserProfileQualification::factory()->count(1)->create(['user_id' => 3, 'qualification_id' => 28, 'future' => 1, 'start_year' => 2025]);
        UserProfileQualification::factory()->count(1)->create(['user_id' => 3, 'qualification_id' => 29, 'future' => 1, 'start_year' => 2026]);
        UserProfile::factory()->count(1)->create(['user_id' => 4, 'intended_university_start_year' => null, 'intended_postgraduate_start_year' => null]);

        Artisan::call('migrate:intended-start-years');

        self::assertEquals(UserProfileQualification::where('user_id', 1)->where('start_year', 2024)->where('qualification_id', 28)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 2)->where('start_year', 2025)->where('qualification_id', 29)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 3)->where('start_year', 2026)->where('qualification_id', 28)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 3)->where('start_year', 2027)->where('qualification_id', 29)->where('future', 1)->count(), 1);
        self::assertEquals(UserProfileQualification::where('user_id', 4)->count(), 0);
    }
}
