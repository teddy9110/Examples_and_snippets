<?php

declare(strict_types=1);

namespace Tests\Unit\App\Support\TSRGUserAPI;

use App\Support\TSRGUserAPI;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GetUserByEmailTest extends TestCase
{
    public function testSuccess()
    {
        $emailAddress = 'test@test.com';
        $mockResponseData = [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'John Doe'
                ]
            ]
        ];

        Http::fake([
            'http://mock-tsrg-users/tsrg_user/v1/user?email=test%40test.com' => Http::response($mockResponseData, 200)
        ]);

        $userData = TSRGUserAPI::getUserByEmail($emailAddress);

        Http::assertSent(function ($request) use ($emailAddress) {
            return $request->url() == 'http://mock-tsrg-users/tsrg_user/v1/user?email=test%40test.com'
                && $request->hasHeader('x-internal-key', 'test_internal_key');
        });

        $this->assertEquals($mockResponseData['data'][0], $userData);
    }

    public function testFailedResponse()
    {
        $emailAddress = 'test@test.com';

        Http::fake([
            'http://mock-tsrg-users/tsrg_user/v1/user?email=test%40test.com' => Http::response([], 404)
        ]);

        $userData = TSRGUserAPI::getUserByEmail($emailAddress);

        Http::assertSent(function ($request) use ($emailAddress) {
            return $request->url() == 'http://mock-tsrg-users/tsrg_user/v1/user?email=test%40test.com'
                && $request->hasHeader('x-internal-key', 'test_internal_key');
        });

        $this->assertNull($userData);
    }
}
