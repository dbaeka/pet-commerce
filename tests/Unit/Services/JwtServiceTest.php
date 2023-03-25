<?php

namespace Tests\Unit\Services;

use App\Dtos\User;
use App\Exceptions\InvalidPathException;
use App\Exceptions\Jwt\InvalidJwtExpiryException;
use App\Exceptions\Jwt\InvalidJwtIssuerException;
use App\Exceptions\Jwt\InvalidJwtTokenException;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Services\Interfaces\JwtTokenProviderInterface;
use App\Services\JwtService;
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
        $service = new JwtService();

        $this->assertInstanceOf(JwtService::class, $service);
    }

    public function testFailsCreateServiceWhenInvalidPrivateKey(): void
    {
        $this->expectException(InvalidPathException::class);

        config([
            'jwt.private_key' => '',
            'jwt.public_key' => self::PUBLIC_KEY
        ]);
        new JwtService();
    }

    public function testFailsCreateServiceWhenInvalidPublicKey(): void
    {
        $this->expectException(InvalidPathException::class);

        config([
            'jwt.private_key' => self::PRIVATE_KEY,
            'jwt.public_key' => ''
        ]);
        new JwtService();
    }

    public function testFailsCreateServiceWhenInvalidIssuer(): void
    {
        $this->expectException(InvalidJwtIssuerException::class);

        config([
            'app.url' => '',
        ]);
        new JwtService();
    }

    public function testFailsCreateServiceWhenExpiryNotNumeric(): void
    {
        $this->expectException(InvalidJwtExpiryException::class);

        config([
            'jwt.expiry_seconds' => 'test'
        ]);

        new JwtService();
    }

    public function testFailsCreateServiceWhenExpiryIsZero(): void
    {
        $this->expectException(InvalidJwtExpiryException::class);

        config([
            'jwt.expiry_seconds' => 0
        ]);

        new JwtService();
    }

    public function testGeneratesTokenReturnTokenString(): void
    {
        $user_uuid = 'foobar';
        $user = User::make([
            'id' => 1,
            'uuid' => $user_uuid
        ]);

        $service = new JwtService();

        $token = $service->generateToken($user);

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

        $service = new JwtService();

        $user = $service->authenticate($token);

        $this->assertNotNull($user);
        $this->assertSame($user_dto, $user);
    }

    protected function getValidToken(): string
    {
        /** @var non-empty-string $token */
        $token = @file_get_contents(base_path('tests/Fixtures/Services/sample-jwt'));
        return trim($token);
    }

    public function testValidateTokenReturnTrue(): void
    {
        $token = 'foobar';

        $this->mock(JwtTokenProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->andReturn(User::make());
        });

        $service = new JwtService();

        $valid = $service->validateToken($token);

        $this->assertTrue($valid);
    }

    public function testFailValidateTokenFailedToAuthenticateReturnFalse(): void
    {
        $token = 'foobar';

        $this->mock(JwtTokenProviderInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticate')
                ->once()
                ->andReturnNull();
        });

        $service = new JwtService();

        $valid = $service->validateToken($token);

        $this->assertFalse($valid);
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

        $service = new JwtService();

        $user = $service->authenticate($token);

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

        $service = new JwtService();

        $user = $service->authenticate($token);

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

        $service = new JwtService();

        $service->authenticate($token);
    }

    /**
     * @param string $token
     * @return void
     * @dataProvider provideInvalidToken
     */
    public function testFailsValidateInvalidTokenThrowsException(string $token): void
    {
        $this->expectException(InvalidJwtTokenException::class);

        $service = new JwtService();

        $service->validateToken($token);
    }

    /**
     * @return void
     * @throws InvalidJwtTokenException
     */
    public function testGetsPayloadTokenReturnArray(): void
    {
        $token = $this->getValidToken();

        $service = new JwtService();

        $payload = $service->getPayload($token);

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('iss', $payload);
        $this->assertArrayHasKey('jti', $payload);
        $this->assertArrayHasKey('user_uuid', $payload);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
    }

    /**
     * @param string $token
     * @return void
     * @throws InvalidJwtTokenException
     * @dataProvider provideInvalidToken
     */
    public function testFailsGetPayloadInvalidTokenThrowsException(string $token): void
    {
        $this->expectException(InvalidJwtTokenException::class);

        $service = new JwtService();

        $service->getPayload($token);
    }

        /**
     * @throws InvalidJwtTokenException
     */
    public function testGetsUserFromValidTokenReturnUser(): void
    {
        $token = $this->getValidToken();

        $this->mock(JwtTokenRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUserByUniqueId')
                ->once()
                ->andReturn(User::make());
        });

        $service = new JwtService();

        $user = $service->getUserFromToken($token);

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @throws InvalidJwtTokenException
     */
    public function testFailsGetsUserNoUserAssociatedWithValidTokenReturnNull(): void
    {
        $token = $this->getValidToken();

        $this->mock(JwtTokenRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getUserByUniqueId')
                ->once()
                ->andReturnNull();
        });

        $service = new JwtService();

        $user = $service->getUserFromToken($token);

        $this->assertNull($user);
    }

    /**
     * @param string $token
     * @return void
     * @throws InvalidJwtTokenException
     * @dataProvider provideInvalidToken
     */
    public function testFailsGetUserInvalidTokenThrowsException(string $token): void
    {
        $this->expectException(InvalidJwtTokenException::class);

        $service = new JwtService();

        $service->getUserFromToken($token);
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
