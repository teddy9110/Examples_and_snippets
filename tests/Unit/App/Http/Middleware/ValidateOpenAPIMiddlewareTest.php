<?php

namespace Tests\Unit\App\Http\Middleware;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Exception;
use App\Http\Middleware\ValidateOpenAPIMiddleware;

class ValidateOpenAPIMiddlewareTest extends TestCase
{
    /**
     * Check that we can read the openapi yml when no cache file exists
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testReadsFileSchema(): void
    {

        $selfMock = Mockery::mock('App\Http\Middleware\ValidateOpenAPIMiddleware[loadCachedSchema]');
        $selfMock->shouldReceive('loadCachedSchema')->once()->andThrow(new Exception);

        $mock = Mockery::mock('League\OpenAPIValidation\PSR7\ValidatorBuilder');
        $mock->shouldNotReceive('fromSchema');
        $mock->shouldReceive('fromYamlFile')->withAnyArgs();

        $request = $this->createFakeRequestObject('GET', [], '', $this->apiPrefix . '/version');

        $selfMock->handle($request, function ($req) use ($request) {
            self::assertEquals($req, $request);
        });
    }

    /**
     * Check that we can read the schema file
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @return void
     */
    public function testReadsCachedSchema(): void
    {
        // Create cache file
        Artisan::call('cache-schema');

        $mock = Mockery::mock('League\OpenAPIValidation\PSR7\ValidatorBuilder');
        $mock->shouldReceive('fromSchema')->withAnyArgs();
        $mock->shouldNotReceive('fromYamlFile');

        $request = $this->createFakeRequestObject('GET', [], '', $this->apiPrefix . '/version');
        $middleware = new ValidateOpenAPIMiddleware();

        $middleware->handle($request, function ($req) use ($request) {
            self::assertEquals($req, $request);
        });

        // Clean up the cache file
        unlink(storage_path('app/openapi.php'));
    }
}
