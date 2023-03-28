<?php

namespace Application;

use App\Models\Post;
use Database\Factories\PostFactory;
use Database\Factories\PromotionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Application\ApiTestCase;

class MainPageTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testFetchPost(): void
    {
        $endpoint = self::PREFIX . 'main/blogs/';

        /** @var Post $post */
        $post = PostFactory::new()->create();

        $this->get($endpoint . $post->uuid)
            ->assertOk()
            ->assertJsonStructure($this->mergeDefaultFields())
            ->assertJsonFragment([
                'success' => 1,
                'uuid' => $post->uuid
            ]);
    }

    public function testGetPostList(): void
    {
        $endpoint = self::PREFIX . 'main/blogs';

        PostFactory::new()->count(65)->create();

        $this->get($endpoint . '?limit=60')
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['slug', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(60, 'data');

        $this->get($endpoint . '?limit=10')
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->get($endpoint . '?page=2&limit=30')
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);
    }

    public function testGetPromotions(): void
    {
        $endpoint = self::PREFIX . 'main/promotions';

        PromotionFactory::new()->invalid()->count(65)->create();

        $this->get($endpoint . '?limit=60')
            ->assertOk()
            ->assertJsonStructure(array_merge($this->mergeDefaultFields(), [
                'meta' => ['total', 'to'], 'links', 'data' => ['*' => ['metadata', 'uuid']]
            ]))
            ->assertJsonFragment([
                'success' => 1,
            ])->assertJsonCount(60, 'data');

        $this->get($endpoint . '?limit=10')
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $this->get($endpoint . '?page=2&limit=30')
            ->assertOk()
            ->assertJsonFragment([
                'current_page' => 2,
            ]);

        $this->get($endpoint . '?limit=100&valid=false')
            ->assertOk()
            ->assertJsonCount(65, 'data');

        PromotionFactory::new()->valid()->count(5)->create();

//        $this->get($endpoint . '?limit=100&valid=true')
//            ->assertOk()
//            ->assertJsonCount(5, 'data');
    }
}
