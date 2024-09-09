<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileLearningProviderController;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PatchTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Ensure PATCH /profile/{id}/learning_provider/current doesn't exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testPATCHDoesntExist(): void
    {
        $response = $this->patch($this->apiPrefix . '/profile/1/learning_provider/current');
        $response = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(
            'The PATCH method is not supported for route ' . ltrim($this->apiPrefix, '/') . '/profile/1/learning_provider/current. Supported methods: PUT.',
            $response['errors'][0]['message']
        );
    }
}
