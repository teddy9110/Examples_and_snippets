<?php

namespace Tests\Feature\App\Http\Controllers\UserProfileSubjectController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
    * Ensure DELETE /profile/{id}/subject/current doesn't exist
    *
    * @return void
    *
    * @throws \JsonException
    */
   public function testDELETEDoesntExist(): void
   {
       $response = $this->delete($this->apiPrefix . '/profile/1/subject/current');
       $response = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

       self::assertEquals(
           'The DELETE method is not supported for route ' . ltrim($this->apiPrefix, '/') . '/profile/1/subject/current. Supported methods: PUT.',
           $response['errors'][0]['message']
       );
   }
}
