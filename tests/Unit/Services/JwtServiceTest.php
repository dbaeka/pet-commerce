<?php

namespace Tests\Unit\Services;

use App\Dtos\User;
use App\Exceptions\InvalidPathException;
use App\Exceptions\Jwt\InvalidJwtExpiryException;
use App\Exceptions\Jwt\InvalidJwtIssuerException;
use App\Exceptions\Jwt\InvalidJwtTokenException;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Services\Jwt\AuthenticateWithToken;
use App\Services\Jwt\BaseJwtProvider;
use App\Services\Jwt\GenerateToken;
use Carbon\Carbon;
use Mockery\MockInterface;
use Tests\TestCase;

class JwtServiceTest extends TestCase
{
    protected const KEYS = __DIR__ . '/../../Fixtures';
    protected const PUBLIC_KEY = self::KEYS . '/jwt-public.key';
    protected const PRIVATE_KEY = self::KEYS . '/jwt-private.key';

    public function testCreatesServiceWhenValidKeys(): void
    {
        config([
            'jwt.private_key' => self::PRIVATE_KEY,
            'jwt.public_key' => self::PUBLIC_KEY
        ]);
        $service = new class () extends BaseJwtProvider {
        };

        $this->assertInstanceOf(BaseJwtProvider::class, $service);
    }

    public function testFailsCreateServiceWhenInvalidPrivateKey(): void
    {
        $this->expectException(InvalidPathException::class);

        config([
            'jwt.private_key' => '',
            'jwt.public_key' => self::PUBLIC_KEY
        ]);
        new class () extends BaseJwtProvider {
        };
    }

    public function testFailsCreateServiceWhenInvalidPublicKey(): void
    {
        $this->expectException(InvalidPathException::class);

        config([
            'jwt.private_key' => self::PRIVATE_KEY,
            'jwt.public_key' => ''
        ]);
        new class () extends BaseJwtProvider {
        };
    }

    public function testFailsCreateServiceWhenInvalidIssuer(): void
    {
        $this->expectException(InvalidJwtIssuerException::class);

        config([
            'app.url' => '',
        ]);
        new class () extends BaseJwtProvider {
        };
    }

    public function testFailsCreateServiceWhenExpiryNotNumeric(): void
    {
        $this->expectException(InvalidJwtExpiryException::class);

        config([
            'jwt.expiry_seconds' => 'test'
        ]);

        new class () extends BaseJwtProvider {
        };
    }

    public function testFailsCreateServiceWhenExpiryIsZero(): void
    {
        $this->expectException(InvalidJwtExpiryException::class);

        config([
            'jwt.expiry_seconds' => 0
        ]);

        new class () extends BaseJwtProvider {
        };
    }

    public function testGeneratesTokenReturnTokenString(): void
    {
        $user_uuid = 'foobar';
        $user = User::make([
            'id' => 1,
            'uuid' => $user_uuid
        ]);

        $token = app(GenerateToken::class)->execute($user);

        $this->checkJwtToken($token->getTokenValue(), $user_uuid);
    }

    protected function checkJwtToken(string $token, string $user_uuid): void
    {
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);

        $payload = base64_decode($parts[1]);
        $this->assertJson($payload);

        $payload = json_decode($payload, true);
        $this->assertArrayHasKey('iss', $payload);
        $this->assertArrayHasKey('user_uuid', $payload);

        $this->assertSame($user_uuid, $payload['user_uuid']);
    }

    /**
     * @return void
     * @throws InvalidJwtTokenException
     */
    public function testAuthenticatesTokenReturnUser(): void
    {
        $token = $this->getValidToken();

        $user_dto = User::make();

        $this->mock(JwtTokenRepositoryInterface::class, function (MockInterface $mock) use ($user_dto) {
            $mock->shouldReceive('checkTokenExists')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('updateTokenLastUsed')
                ->once();

            $mock->shouldReceive('expireToken')
                ->never();

            $mock->shouldReceive('getUserByUniqueId')
                ->once()
                ->andReturn($user_dto);
        });

        $user = app(AuthenticateWithToken::class)->execute($token);

        $this->assertNotNull($user);
        $this->assertSame($user_dto, $user);
    }

    protected function getValidToken(): string
    {
        /** @var non-empty-string $token */
        $token = @file_get_contents(base_path('tests/Fixtures/Services/sample-jwt'));
        return trim($token);
    }

    /**
     * @return void
     * @throws InvalidJwtTokenException
     */
    public function testFailsAuthenticateExpiredTokenReturnFalse(): void
    {
        $token = $this->getValidToken();

        Carbon::setTestNow(now()->addDay());

        $this->mock(JwtTokenRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkTokenExists')
                ->once()
                ->andReturn(true);

            $mock->shouldReceive('updateTokenLastUsed')
                ->once();

            $mock->shouldReceive('deleteToken')
                ->once();
        });

        $user = app(AuthenticateWithToken::class)->execute($token);

        $this->assertNull($user);
    }

    /**
     * @return void
     * @throws InvalidJwtTokenException
     */
    public function testFailsAuthenticateTokenNotAssociatedWithUserReturnFalse(): void
    {
        $token = $this->getValidToken();

        $this->mock(JwtTokenRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkTokenExists')
                ->once()
                ->andReturn(false);

            $mock->shouldReceive('updateTokenLastUsed')
                ->never();

            $mock->shouldReceive('expireToken')
                ->never();
        });

        $user = app(AuthenticateWithToken::class)->execute($token);

        $this->assertNull($user);
    }

    /**
     * @param string $token
     * @return void
     * @throws InvalidJwtTokenException
     * @dataProvider provideInvalidToken
     */
    public function testFailsAuthenticateInvalidTokenThrowsException(string $token): void
    {
        $this->expectException(InvalidJwtTokenException::class);

        app(AuthenticateWithToken::class)->execute($token);
    }

    /**
     * @param string $token
     * @return void
     * @dataProvider provideInvalidToken
     */
    public function testFailsValidateInvalidTokenThrowsException(string $token): void
    {
        $this->expectException(InvalidJwtTokenException::class);

        app(AuthenticateWithToken::class)->execute($token);
    }

    /**
     * @return array<int, array<int,string>>
     */
    protected function provideInvalidToken(): array
    {
        return [
            [''],
            ['foobar']
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();


        $this->setKeyPaths();
        $this->setIssuer();
        $this->setExpiry();

        Carbon::setTestNow('1997-02-20');
    }

    protected function setKeyPaths(): void
    {
        config([
            'jwt.private_key' => self::PRIVATE_KEY,
            'jwt.public_key' => self::PUBLIC_KEY
        ]);
    }

    protected function setIssuer(string $default = 'https://example.com'): void
    {
        config([
            'app.url' => $default,
        ]);
    }

    protected function setExpiry(int $default = 20): void
    {
        config([
            'jwt.expiry_seconds' => $default,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow();
    }
}
