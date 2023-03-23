<?php

namespace Tests\Feature\Component;

use Tests\TestCase;

class JwtKeysCommandTest extends TestCase
{
    public const KEYS = __DIR__ . '/../../keys';
    public const PUBLIC_KEY = self::KEYS . '/jwt-public.key';
    public const PRIVATE_KEY = self::KEYS . '/jwt-private.key';

    public function testPrivateAndPublicKeysAreGenerated(): void
    {
        $this->assertFileExists(self::PUBLIC_KEY);
        $this->assertFileExists(self::PRIVATE_KEY);
    }

    public function testPrivateAndPublicKeysShouldNotBeGeneratedTwice(): void
    {
        $result = $this->artisan('jwt:keys');
        $expectedOutput = 'Encryption keys already exist. Use the --force option to overwrite them.';
        if (is_int($result)) {
            $this->assertNotEquals(0, $result);
            $this->expectOutputString($expectedOutput);
        } else {
            $result
                ->assertFailed()
                ->expectsOutput($expectedOutput);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'jwt.private_key' => self::PRIVATE_KEY,
            'jwt.public_key' => self::PUBLIC_KEY
        ]);

        @unlink(self::PUBLIC_KEY);
        @unlink(self::PRIVATE_KEY);

        $this->artisan('jwt:keys');
    }

    protected function tearDown(): void
    {
        @unlink(self::PUBLIC_KEY);
        @unlink(self::PRIVATE_KEY);
    }
}
