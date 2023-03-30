<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Category\CategoryListingRequest;
use App\Http\Requests\v1\Category\StoreCategoryRequest;
use App\Http\Requests\v1\Category\UpdateCategoryRequest;
use App\Http\Resources\v1\DefaultCollection;
use App\Http\Resources\v1\BaseResource;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Categories API endpoint"
 * )
 */
class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryRepositoryContract $category_repository
    ) {
        $this->middleware('secure')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     operationId="categories-list",
     *     summary="List all the categories",
     *     tags={"Categories"},
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
    public function index(CategoryListingRequest $request): DefaultCollection
    {
        $categories = $this->category_repository->getList();
        return new DefaultCollection($categories);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     operationId="categories-create",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/StoreCategoryRequest"
     *       )
     *      )
     *     ),
     *     @OA\Response(response=201, ref="#/components/responses/Created"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function store(StoreCategoryRequest $request): BaseResource
    {
        $this->authorize('create', Category::class);
        $data = $request->validated();
        $category = $this->category_repository->create($data);
        return $category ? new BaseResource($category) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{uuid}",
     *     operationId="view-category",
     *     summary="Fetch a category",
     *     tags={"Categories"},
     *     security={{}},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(string $uuid): BaseResource
    {
        $category = $this->category_repository->findByUuid($uuid);
        return $category ? new BaseResource($category) : throw new ModelNotFoundException();
    }

    /**
     * @OA\Put(
     *     path="/api/v1/categories/{uuid}",
     *     operationId="category-edit",
     *     summary="Edit a category",
     *     tags={"Categories"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/UpdateCategoryRequest"
     *       )
     *      )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function update(UpdateCategoryRequest $request, string $uuid): BaseResource
    {
        $this->authorize('update', Category::class);
        $data = $request->validated();
        $category = $this->category_repository->updateByUuid($uuid, $data);
        return $category ? new BaseResource($category) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{uuid}",
     *     operationId="category-delete",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=204, ref="#/components/responses/NoContent"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function destroy(string $uuid): Response
    {
        $this->authorize('delete', Category::class);
        $success = $this->category_repository->deleteByUuid($uuid);
        return $success ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }
}
