<?php

namespace Application;

use App\Models\User;
use Database\Factories\OrderFactory;
use Database\Factories\UserFactory;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Application\ApiTestCase;

class UserTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testUserLogin(): void
    {
        $endpoint = self::PREFIX . 'user/login';
        $email = 'delmwin@test.com';

        /** @var User $user */
        $user = UserFactory::new()->create([
            'email' => $email,
            'password' => Hash::make('secret'),
        ]);

        $this->post($endpoint, [
            'email' => $email,
            'password' => 'secret',
        ])->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields('token'))
            ->assertJsonFragment([
                'success' => 1
            ]);

        self::assertSame(1, $user->jwt_tokens()->count());

        $this->post($endpoint, [
            'email' => $email,
            'password' => 'wrong-secret',
        ])->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ])
            ->assertUnauthorized();

        UserFactory::new()->create([
            'email' => 'regular@test.com',
            'is_admin' => true,
            'password' => Hash::make('secret'),
        ]);

        $this->post($endpoint, [
            'email' => 'regular@test.com',
            'password' => 'secret',
        ])->assertUnauthorized()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testUserLoginWithWrongParamsFails(): void
    {
        $endpoint = self::PREFIX . 'user/login';

        $this->post($endpoint, [
            'password' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testUserLogout(): void
    {
        $login_endpoint = self::PREFIX . 'user/login';
        $logout_endpoint = self::PREFIX . 'user/logout';

        $email = 'delmwin@test.com';

        /** @var User $user */
        $user = UserFactory::new()->create([
            'email' => $email,
            'password' => Hash::make('secret'),
        ]);

        $response = $this->post($login_endpoint, [
            'email' => $email,
            'password' => 'secret',
        ]);

        self::assertSame(1, $user->jwt_tokens()->count());

        $this->withToken($response->json('data.token'))
            ->get($logout_endpoint)
            ->assertNoContent();

        self::assertSame(0, $user->jwt_tokens()->count());
    }

    public function testEditUser(): void
    {
        $endpoint = self::PREFIX . 'user/edit';
        /** @var User $user */
        $user = UserFactory::new()->create([
            'first_name' => 'Delmwin',
            'last_name' => 'Baeka',
            'password' => 'secret'
        ]);

        $data = array_merge($user->getAttributes(), [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "password" => "secret1234",
            "password_confirmation" => "secret1234"
        ]);

        self::assertNotSame($user->first_name, $data['first_name']);
        self::assertNotSame($user->last_name, $data['last_name']);

        $unchanged = $user->only(['email', 'address', 'phone_number']);

        $this->putAs($endpoint, $data, $user)
            ->assertOk();

        $user->refresh();

        self::assertSame($user->first_name, $data['first_name']);
        self::assertSame($user->last_name, $data['last_name']);
        self::assertEquals($unchanged, $user->only(['email', 'address', 'phone_number']));
    }

    public function testDeleteUser(): void
    {
        $endpoint = self::PREFIX . 'user';
        /** @var User $user */
        $user = UserFactory::new()->create();
        $uuid = $user->uuid;

        self::assertDatabaseHas('users', [
            'uuid' => $uuid
        ]);

        $this->deleteAs($endpoint, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('users', [
            'uuid' => $uuid
        ]);
    }

    public function testViewUser(): void
    {
        $endpoint = self::PREFIX . 'user';

        /** @var User $user */
        $user = UserFactory::new()->create(['first_name' => 'dem']);

        $this->getAs($endpoint, $user)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'first_name' => 'dem'
            ]);
    }

    public function testUserCreate(): void
    {
        $endpoint = self::PREFIX . 'user/create';

        UserFactory::new()->create();

        $data = [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "email" => "delmwin@test.com",
            "password" => 'foobar1234',
            "password_confirmation" => 'foobar1234',
            "address" => fake()->streetAddress(),
            "phone_number" => fake()->e164PhoneNumber(),
        ];

        $this->post($endpoint, $data)->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "first_name",
                "last_name",
                "email",
                "address",
                "phone_number",
                "updated_at",
                "created_at",
                "token"
            ))
            ->assertJsonFragment([
                'success' => 1,
                'email' => 'delmwin@test.com'
            ]);

        self::assertDatabaseHas('users', [
            'email' => 'delmwin@test.com',
            'is_admin' => false
        ]);

        $this->post($endpoint, [
            'email' => 'regular@test.com',
            'password' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testGetUserOrders(): void
    {
        $endpoint = self::PREFIX . 'user/orders';

        /** @var User $user */
        $user = UserFactory::new()->create();

        OrderFactory::new()->count(40)->create([
            'user_uuid' => $user->uuid
        ]);

        /** @var User $user2 */
        $user2 = UserFactory::new()->create();
        OrderFactory::new()->count(10)->create([
            'user_uuid' => $user2->uuid
        ]);

        $this->getAs($endpoint, $user)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links',
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(20, 'data');

        $this->getAs($endpoint . '?limit=10', $user)
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->getAs($endpoint . '?page=2&limit=30', $user)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }

    public function testUserForgotPassword(): void
    {
        $endpoint = self::PREFIX . 'user/forgot-password';

        UserFactory::new()->create([
            'email' => 'delmwin@test.com'
        ]);

        $data = [
            "email" => "delmwin@test.com",
        ];

        $response = $this->post($endpoint, $data);

        $response->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields(
                "reset_token",
            ))
            ->assertJsonFragment([
                'success' => 1,
            ]);

        self::assertDatabaseHas('password_resets', [
            'email' => 'delmwin@test.com',
            'token' => $response->json('data.reset_token')
        ]);

        $this->post($endpoint, [
            'email' => 'regular@test.com',
        ])->assertNotFound()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testUserResetPassword(): void
    {
        $endpoint = self::PREFIX . 'user/reset-password-token';

        $email = 'delmwin@test.com';
        /** @var User $user */

        $user = UserFactory::new()->create([
            'email' => $email,
            'password' => Hash::make('secret1')
        ]);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => 'secret'
        ]);

        $this->post($endpoint, [
            'email' => $email,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'token' => 'secret'
        ])->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields('message'))
            ->assertJsonFragment([
                'success' => 1,
                "message" => "Password has been successfully updated"
            ]);

        $user->refresh();
        self::assertTrue(Hash::check('secret1234', $user->getAuthPassword()));
    }
}
