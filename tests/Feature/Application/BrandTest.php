<?php

namespace Application;

use App\Models\Brand;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Application\ApiTestCase;

class BrandTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testUpdateBrand(): void
    {
        $endpoint = self::PREFIX . 'brands/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Brand $brand */
        $brand = BrandFactory::new()->create([
            'title' => 'infinity war'
        ]);

        $data = [
            "title" => 'secret wars',
        ];

        self::assertNotSame($brand->title, $data['title']);

        $this->putAs($endpoint . $brand->uuid, $data, $user)
            ->assertOk();

        $brand->refresh();

        self::assertSame($brand->title, $data['title']);
        self::assertSame($brand->slug, Str::slug($data['title']));
    }

    public function testDeleteBrand(): void
    {
        $endpoint = self::PREFIX . 'brands/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Brand $brand */
        $brand = BrandFactory::new()->create();

        self::assertDatabaseHas('brands', [
            'uuid' => $brand->uuid
        ]);

        $this->deleteAs($endpoint . $brand->uuid, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('brands', [
            'uuid' => $brand->uuid
        ]);
    }

    public function testFetchBrand(): void
    {
        $endpoint = self::PREFIX . 'brands/';

        /** @var Brand $brand */
        $brand = BrandFactory::new()->create();

        $this->get($endpoint . $brand->uuid)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $brand->uuid
            ]);
    }

    public function testCreateBrand(): void
    {
        $endpoint = self::PREFIX . 'brands';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $data = [
            "title" => 'foobar baz',
        ];

        $response = $this->postAs($endpoint, $data, $user);

        $response->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "slug",
                "title"
            ))
            ->assertJsonFragment([
                'success' => 1,
                'title' => 'foobar baz'
            ]);

        self::assertDatabaseHas('brands', [
            'uuid' => $response->json('data.uuid'),
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

    public function testGetBrandList(): void
    {
        $endpoint = self::PREFIX . 'brands';

        BrandFactory::new()->count(40)->create();

        $this->get($endpoint)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['title', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(20, 'data');

        $this->get($endpoint . '?limit=10')
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->get($endpoint . '?page=2&limit=30')
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }
}
