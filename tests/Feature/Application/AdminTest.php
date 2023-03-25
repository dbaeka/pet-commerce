<?php

namespace Tests\Feature\Application;

use App\Models\User;
use Database\Factories\UserFactory;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testAdminLogin(): void
    {
        $endpoint = self::PREFIX . 'admin/login';
        $email = 'delmwin@test.com';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create([
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
            'is_admin' => false,
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


    public function testAdminLoginWithWrongParamsFails(): void
    {
        $endpoint = self::PREFIX . 'admin/login';

        $this->post($endpoint, [
            'password' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }


    public function testAdminLogout(): void
    {
        $login_endpoint = self::PREFIX . 'admin/login';
        $logout_endpoint = self::PREFIX . 'admin/logout';

        $email = 'delmwin@test.com';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create([
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

    public function testAdminUserEdit(): void
    {
        $endpoint = self::PREFIX . 'admin/user-edit/';
        /** @var User $user */
        $user = UserFactory::new()->create([
            'first_name' => 'Delmwin',
            'last_name' => 'Baeka',
            'password' => 'secret'
        ]);
        $uuid = $user->uuid;

        /** @var User $admin */
        $admin = UserFactory::new()->admin()->create();

        $data = array_merge($user->getAttributes(), [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "password" => "secret",
            "password_confirmation" => "secret"
        ]);

        self::assertNotSame($user->first_name, $data['first_name']);
        self::assertNotSame($user->last_name, $data['last_name']);

        $unchanged = $user->only(['email', 'address', 'phone_number']);

        $this->putAs($endpoint . $uuid, $data, $admin)
            ->assertOk();

        $user->refresh();

        self::assertSame($user->first_name, $data['first_name']);
        self::assertSame($user->last_name, $data['last_name']);
        self::assertEquals($unchanged, $user->only(['email', 'address', 'phone_number']));
    }

    public function testAdminUserDelete(): void
    {
        $endpoint = self::PREFIX . 'admin/user-delete/';
        /** @var User $user */
        $user = UserFactory::new()->create();
        $uuid = $user->uuid;

        self::assertDatabaseHas('users', [
            'uuid' => $uuid
        ]);

        /** @var User $admin */
        $admin = UserFactory::new()->admin()->create();

        $this->deleteAs($endpoint . $uuid, [], $admin)
            ->assertNoContent();

        self::assertDatabaseMissing('users', [
            'uuid' => $uuid
        ]);

        $this->deleteAs($endpoint . 'non-existing', [], $admin)
            ->assertNotFound();
    }

    public function testAdminUserListing(): void
    {
        $endpoint = self::PREFIX . 'admin/user-listing';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create(['first_name' => 'dem']);

        UserFactory::new()->count(40)->create();

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

        $this->getAs($endpoint . '?page=2&limit=10', $user)
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);

        UserFactory::new()->create([
            'first_name' => 'delmwin'
        ]);

        $this->getAs($endpoint . '?first_name=delmwin', $user)
            ->assertOk()
            ->assertJsonFragment([
                'first_name' => 'delmwin',
            ])
            ->assertJsonCount(1, 'data');
    }

    public function testAdminCreate(): void
    {
        $endpoint = self::PREFIX . 'admin/create';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $data = [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "email" => "delmwin@test.com",
            "password" => 'foobar',
            "password_confirmation" => 'foobar',
            "address" => fake()->streetAddress(),
            "phone_number" => fake()->e164PhoneNumber(),
        ];

        $this->postAs($endpoint, $data, $user)->assertOk()
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
            'is_admin' => true
        ]);

        $this->postAs($endpoint, [
            'email' => 'regular@test.com',
            'password' => 'secret',
        ], $user)->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testAdminCreateNonAuthorizedUserFails(): void
    {
        $endpoint = self::PREFIX . 'admin/create';

        $data = [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "email" => "delmwin@test.com",
            "password" => 'foobar',
            "password_confirmation" => 'foobar',
            "address" => fake()->streetAddress(),
            "phone_number" => fake()->e164PhoneNumber(),
        ];

        /** @var User $user */
        $user = UserFactory::new()->create([
            'email' => 'regular2@test.com',
            'is_admin' => false,
            'password' => Hash::make('secret'),
        ]);

        $this->postAs($endpoint, $data, $user)
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ])
            ->assertForbidden();
    }
}
