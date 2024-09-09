<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileLearningProviderController;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Ensure DELETE /profile/{id}/learning_provider/current doesn't exist
     *
     * @return void
     *
     * @throws \JsonException
     */
    public function testDELETEDoesntExist(): void
    {
        $response = $this->delete($this->apiPrefix . '/profile/1/learning_provider/current');
        $response = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(
            'The DELETE method is not supported for route ' . ltrim($this->apiPrefix, '/') . '/profile/1/learning_provider/current. Supported methods: PUT.',
            $response['errors'][0]['message']
        );
    }
}
