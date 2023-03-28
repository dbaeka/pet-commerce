<?php

namespace Application;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Values\ProductMetadata;
use Database\Factories\BrandFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\ProductFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Application\ApiTestCase;

class ProductTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testUpdateProduct(): void
    {
        $endpoint = self::PREFIX . 'products/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $data = ProductFactory::new()->definition();

        /** @var Product $product */
        $product = ProductFactory::new()->create($data);


        $data["title"] = 'secret wars';


        self::assertNotSame($product->title, $data['title']);

        $this->putAs($endpoint . $product->uuid, $data, $user)
            ->assertOk()
            ->assertJsonStructure([
                'success', 'data' => ['brand']
            ]);

        $product->refresh();

        self::assertSame($product->title, $data['title']);
    }

    public function testDeleteProduct(): void
    {
        $endpoint = self::PREFIX . 'products/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Product $product */
        $product = ProductFactory::new()->create();

        self::assertDatabaseHas('products', [
            'uuid' => $product->uuid
        ]);

        $this->deleteAs($endpoint . $product->uuid, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('products', [
            'uuid' => $product->uuid
        ]);
    }

    public function testFetchProduct(): void
    {
        $endpoint = self::PREFIX . 'products/';

        /** @var Product $product */
        $product = ProductFactory::new()->create();

        $this->get($endpoint . $product->uuid)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $product->uuid
            ]);
    }

    public function testCreateProduct(): void
    {
        $endpoint = self::PREFIX . 'products';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        $data = ProductFactory::new()->definition();

        $response = $this->postAs($endpoint, $data, $user);

        $response->assertCreated()
            ->assertJsonStructure($this->mergeDefaultFields(
                "uuid",
                "brand",
                "title"
            ))
            ->assertJsonFragment([
                'success' => 1,
                'title' => $data['title']
            ]);

        self::assertDatabaseHas('products', [
            'uuid' => $response->json('data.uuid'),
        ]);

        $this->post($endpoint, [
            'title' => 'regular@test.com',
            'password' => 'secret',
        ])->assertUnprocessable()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 0
            ]);
    }

    public function testGetProductList(): void
    {
        $endpoint = self::PREFIX . 'products';

        ProductFactory::new()->count(40)->create();

        $this->get($endpoint)
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['brand', 'category_uuid', 'category']]
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

        /** @var Category $category */
        $category = CategoryFactory::new()->create();
        ProductFactory::new()->count(5)->create([
            'category_uuid' => $category->uuid
        ]);

        $this->get($endpoint . '?limit=100&category_uuid=' . $category->uuid)
            ->assertOk()
            ->assertJsonCount(5, 'data');

        ProductFactory::new()->create([
            'uuid' => 'foobar'
        ]);

        $this->get($endpoint . '?limit=100&uuid=foobar')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        /** @var Brand $brand */
        $brand = BrandFactory::new()->create();
        ProductFactory::new()->count(14)->create([
           'metadata' => new ProductMetadata(
               $brand->uuid,
               fake()->uuid()
           )
        ]);
        $this->get($endpoint . '?limit=100&brand_uuid=' . $brand->uuid)
            ->assertOk()
            ->assertJsonCount(14, 'data');
    }
}
