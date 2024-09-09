<?php

namespace Tests\Unit\App\Support\UserProfileHelper;

use App\Exceptions\SchemaValidationException;
use App\Support\UserProfileHelper;
use Mockery;
use Tests\TestCase;

class ValidateStatusTest extends TestCase
{
    /**
     * Ensure that an exception is thrown if no status is true
     *
     * @return void
     */
    public function testThrowsExceptionIfNoStatusTrue(): void
    {
        $this->expectException(SchemaValidationException::class);

        UserProfileHelper::validateStatus(false, false, false);
    }

    /**
     * Ensure that an exception is thrown if more than one status is true
     *
     * @return void
     */
    public function testThrowsExceptionIfMoreThanOneStatusTrue(): void
    {
        $this->expectException(SchemaValidationException::class);

        UserProfileHelper::validateStatus(true, true, false);
    }

    /**
     * Test function returns nothing if status is valid
     *
     * @return void
     */
    public function testReturnsNothingIfStatusValid(): void
    {
        $this->assertNull(UserProfileHelper::validateStatus(false, true, false));
    }
}
