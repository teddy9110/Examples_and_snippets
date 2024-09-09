<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileSubjectController;

use Tests\TestCase;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Faker\Factory;
use App\Models\VBulletinReputation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Http;

class GetTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Ensure GET /profile/{id}/subject/current doesn't exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testGETDoesntExist(): void
    {
        $response = $this->get($this->apiPrefix . '/profile/1/subject/current');
        $response = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(
            'The GET method is not supported for route ' . ltrim($this->apiPrefix, '/') . '/profile/1/subject/current. Supported methods: PUT.',
            $response['errors'][0]['message']
        );
    }
}
