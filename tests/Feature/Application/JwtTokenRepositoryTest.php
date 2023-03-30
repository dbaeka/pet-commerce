<?php

namespace Tests\Feature\Application;

use App\Dtos\Token;
use App\Dtos\User;
use App\Models\JwtToken;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use Carbon\Carbon;
use Database\Factories\JwtTokenFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JwtTokenRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private JwtTokenRepositoryContract $jwt_repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->jwt_repository = app(JwtTokenRepositoryContract::class);
    }

    public function testCreateToken(): void
    {
        /** @var JwtToken $jwt_token */
        $jwt_token = JwtTokenFactory::new()->make();
        $token = Token::make($jwt_token->getAttributes());
        $token_id = $this->jwt_repository->createToken($token);
        self::assertNotEmpty($token_id);
        self::assertDatabaseHas('jwt_tokens', [
            'id' => $token_id,
            'unique_id' => $token->unique_id
        ]);
    }

    public function testCheckTokenExists(): void
    {
        /** @var JwtToken $jwt_token */
        $jwt_token = JwtTokenFactory::new()->create();

        $exists = $this->jwt_repository->checkTokenExists($jwt_token->unique_id);
        self::assertTrue($exists);

        $exists = $this->jwt_repository->checkTokenExists('foobar');
        self::assertFalse($exists);
    }

    public function testUpdateTokenLastUsed(): void
    {
        $expected_date = Carbon::make('1997-02-20');
        /** @var JwtToken $jwt_token */
        $jwt_token = JwtTokenFactory::new()->create([
            'last_used_at' => Carbon::make('2020-02-20')
        ]);

        Carbon::setTestNow($expected_date);
        self::assertNotEquals($expected_date, $jwt_token->last_used_at);

        $updated = $this->jwt_repository->updateTokenLastUsed($jwt_token->unique_id);
        self::assertTrue($updated);
        self::assertEquals($expected_date, $jwt_token->refresh()->last_used_at);
    }

    public function testGetUserByUniqueId(): void
    {
        /** @var \App\Models\User $user_model */
        $user_model = UserFactory::new()->create();

        /** @var JwtToken $jwt_token */
        $jwt_token = JwtTokenFactory::new()->create([
            'user_uuid' => $user_model->uuid
        ]);

        $user = $this->jwt_repository->getUserByUniqueId($jwt_token->unique_id);
        self::assertNotNull($user);
        self::assertInstanceOf(User::class, $user);
        self::assertSame($user_model->uuid, $user->uuid);
    }

    public function testExpireToken(): void
    {
        /** @var JwtToken $jwt_token */
        $jwt_token = JwtTokenFactory::new()->create();

        self::assertDatabaseHas('jwt_tokens', [
            'unique_id' => $jwt_token->unique_id
        ]);

        $deleted = $this->jwt_repository->deleteToken($jwt_token->unique_id);
        self::assertTrue($deleted);

        self::assertDatabaseMissing('jwt_tokens', [
            'unique_id' => $jwt_token->unique_id
        ]);
    }
}
