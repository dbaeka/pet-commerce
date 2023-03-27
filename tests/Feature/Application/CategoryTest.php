<?php

namespace Application;

use App\Models\Category;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Application\ApiTestCase;

class CategoryTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testUpdateCategory(): void
    {
        $endpoint = self::PREFIX . 'categories/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Category $category */
        $category = CategoryFactory::new()->create([
            'title' => 'infinity war'
        ]);

        $data = [
            "title" => 'secret wars',
        ];

        self::assertNotSame($category->title, $data['title']);

        $this->putAs($endpoint . $category->uuid, $data, $user)
            ->assertOk();

        $category->refresh();

        self::assertSame($category->title, $data['title']);
        self::assertSame($category->slug, Str::slug($data['title']));
    }

    public function testDeleteCategory(): void
    {
        $endpoint = self::PREFIX . 'categories/';

        /** @var User $user */
        $user = UserFactory::new()->admin()->create();

        /** @var Category $category */
        $category = CategoryFactory::new()->create();

        self::assertDatabaseHas('categories', [
            'uuid' => $category->uuid
        ]);

        $this->deleteAs($endpoint . $category->uuid, [], $user)
            ->assertNoContent();

        self::assertDatabaseMissing('categories', [
            'uuid' => $category->uuid
        ]);
    }

    public function testFetchCategory(): void
    {
        $endpoint = self::PREFIX . 'categories/';

        /** @var Category $category */
        $category = CategoryFactory::new()->create();

        $this->get($endpoint . $category->uuid)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $category->uuid
            ]);
    }

    public function testCreateCategory(): void
    {
        $endpoint = self::PREFIX . 'categories';

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

        self::assertDatabaseHas('categories', [
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

    public function testGetCategoryList(): void
    {
        $endpoint = self::PREFIX . 'categories';

        CategoryFactory::new()->count(40)->create();

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
