<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\MainPage\PostListingRequest;
use App\Http\Requests\v1\Product\ProductListingRequest;
use App\Http\Resources\v1\BaseResource;
use App\Http\Resources\v1\DefaultCollection;
use App\Repositories\PostRepository;
use App\Repositories\PromotionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(
 *     name="Main Page",
 *     description="Main Page API endpoint"
 * )
 */
class MainPageController extends Controller
{
    public function __construct(
        private readonly PostRepository      $post_repository,
        private readonly PromotionRepository $promotion_repository
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/main/promotions",
     *     operationId="main-promotions-list",
     *     summary="List all the promotions",
     *     tags={"Main Page"},
     *     security={{}},
     *     @OA\Parameter(ref="#/components/parameters/page_query"),
     *     @OA\Parameter(ref="#/components/parameters/limit_query"),
     *     @OA\Parameter(ref="#/components/parameters/sort_by_query"),
     *     @OA\Parameter(ref="#/components/parameters/desc_query"),
     *     @OA\Parameter(ref="#/components/parameters/valid_query"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function getPromotions(ProductListingRequest $request): DefaultCollection
    {
        if ($request->has('valid')) {
            $valid = $request->boolean('valid');
            if ($valid) {
                $promotions = $this->promotion_repository->getValidList();
            } else {
                $promotions = $this->promotion_repository->getInValidList();
            }
        } else {
            $promotions = $this->promotion_repository->getList();
        }
        return new DefaultCollection($promotions);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/main/blogs",
     *     operationId="main-blogs-list",
     *     summary="List all posts",
     *     tags={"Main Page"},
     *     security={{}},
     *     @OA\Parameter(ref="#/components/parameters/page_query"),
     *     @OA\Parameter(ref="#/components/parameters/limit_query"),
     *     @OA\Parameter(ref="#/components/parameters/sort_by_query"),
     *     @OA\Parameter(ref="#/components/parameters/desc_query"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function getBlogs(PostListingRequest $request): DefaultCollection
    {
        $posts = $this->post_repository->getList();
        return new DefaultCollection($posts);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/main/blogs/{uuid}",
     *     operationId="main-blogs-show",
     *     summary="Fetch a post",
     *     tags={"Main Page"},
     *     security={{}},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function showBlog(string $uuid): BaseResource
    {
        $post = $this->post_repository->findByUuid($uuid);
        return $post ? new BaseResource($post) : throw new ModelNotFoundException();
    }
}
