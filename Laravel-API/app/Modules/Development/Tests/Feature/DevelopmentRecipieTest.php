<?php

namespace Rhf\Modules\Development\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Rhf\Modules\User\Models\UserRole;
use Tests\Feature\ApiTest;

class DevelopmentRecipieTest extends ApiTest
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupUser();
    }

    public function testThatRecipieCanBeCreated()
    {
        $response = $this->getUserResponse(
            'GET',
            '/development/create-recipe'
        );
        $response->assertStatus(201);
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'active',
                    'title',
                    'serves',
                    'prep_time',
                    'total_time',
                    'image_uri',
                    'macro' => [],
                    'ingredients' => [],
                    'instructions' => [],
                    'created_at',
                    'updated_at',
                ]
            ]
        );
    }
}
