<?php

declare(strict_types=1);

namespace Tests\Unit\App\Support\TSRGUserAPI;

use App\Support\TSRGUserAPI;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    public function testGetUserSuccess()
    {
        $userId = 12345;
        $mockResponseData = [
            'data' => [
                'id' => $userId,
                'name' => 'John Doe'
            ]
        ];

        Http::fake([
            'http://mock-tsrg-users/tsrg_user/v1/user/' . $userId => Http::response($mockResponseData, 200)
        ]);

        $userData = TSRGUserAPI::getUser($userId);

        Http::assertSent(function ($request) use ($userId) {
            return $request->url() == 'http://mock-tsrg-users/tsrg_user/v1/user/' . $userId
                && $request->hasHeader('x-internal-key', 'test_internal_key');
        });

        $this->assertEquals($mockResponseData['data'], $userData);
    }

    public function testGetUserFailedResponse()
    {
        $userId = 12345;

        Http::fake([
            'http://mock-tsrg-users/tsrg_user/v1/user/' . $userId => Http::response([], 404)
        ]);

        $userData = TSRGUserAPI::getUser($userId);

        Http::assertSent(function ($request) use ($userId) {
            return $request->url() == 'http://mock-tsrg-users/tsrg_user/v1/user/' . $userId
                && $request->hasHeader('x-internal-key', 'test_internal_key');
        });

        $this->assertNull($userData);
    }
}
