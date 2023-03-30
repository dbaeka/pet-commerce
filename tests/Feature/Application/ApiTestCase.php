<?php

namespace Tests\Feature\Application;

use App\Models\User;
use App\Services\Auth\LoginWithId;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Throwable;

class ApiTestCase extends TestCase
{
    protected const KEYS = __DIR__ . '/../../Fixtures';
    protected const PUBLIC_KEY = self::KEYS . '/jwt-public.key';
    protected const PRIVATE_KEY = self::KEYS . '/jwt-private.key';
    protected const PREFIX = 'api/v1/';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'jwt.private_key' => self::PRIVATE_KEY,
            'jwt.public_key' => self::PUBLIC_KEY
        ]);
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function mergeDefaultFields(string ...$fields): array
    {
        return [
            'data' => $fields,
            'success', "error", "errors",
        ];
    }

    /**
     * @throws Throwable
     */
    protected function getAs(string $url, ?User $user = null): TestResponse
    {
        return $this->jsonAs($user, 'get', $url);
    }

    /**
     * @param User|null $user
     * @param string $method
     * @param string $uri
     * @param array<string, mixed> $data
     * @return TestResponse
     * @throws Throwable
     */
    private function jsonAs(?User $user, string $method, string $uri, array $data = []): TestResponse
    {
        $user = $user ?: User::factory()->create(['is_admin' => false]);
        $token = app(LoginWithId::class)->execute($user->id);
        throw_if(empty($token));
        return parent::withToken($token)->json($method, $uri, $data);
    }

    /**
     * @param string $url
     * @param array<string, mixed> $data
     * @param User|null $user
     * @return TestResponse
     * @throws Throwable
     */
    protected function deleteAs(string $url, array $data = [], ?User $user = null): TestResponse
    {
        return $this->jsonAs($user, 'delete', $url, $data);
    }

    /**
     * @param string $url
     * @param array<string, mixed> $data
     * @param User|null $user
     * @return TestResponse
     * @throws Throwable
     */
    protected function postAs(string $url, array $data, ?User $user = null): TestResponse
    {
        return $this->jsonAs($user, 'post', $url, $data);
    }

    /**
     * @param string $url
     * @param array<string, mixed> $data
     * @param User|null $user
     * @return TestResponse
     * @throws Throwable
     */
    protected function putAs(string $url, array $data, ?User $user = null): TestResponse
    {
        return $this->jsonAs($user, 'put', $url, $data);
    }

    /**
     * @param string $url
     * @param array<string, mixed> $data
     * @param User|null $user
     * @return TestResponse
     * @throws Throwable
     */
    protected function patchAs(string $url, array $data, ?User $user = null): TestResponse
    {
        return $this->jsonAs($user, 'patch', $url, $data);
    }
}
