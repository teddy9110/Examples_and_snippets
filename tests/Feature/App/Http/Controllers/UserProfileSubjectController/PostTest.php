<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileSubjectController;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Ensure POST /profile/{id}/subject/current doesn't exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPOSTDoesntExist(): void
    {
        $response = $this->post($this->apiPrefix . '/profile/1/subject/current');
        $response = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(
            'The POST method is not supported for route ' . ltrim($this->apiPrefix, '/') . '/profile/1/subject/current. Supported methods: PUT.',
            $response['errors'][0]['message']
        );
    }
}
