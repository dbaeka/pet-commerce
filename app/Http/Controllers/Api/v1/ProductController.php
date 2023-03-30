<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Product\ProductListingRequest;
use App\Http\Requests\v1\Product\StoreProductRequest;
use App\Http\Requests\v1\Product\UpdateProductRequest;
use App\Http\Resources\v1\BaseResource;
use App\Http\Resources\v1\DefaultCollection;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryContract;
use App\Values\ProductMetadata;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Products API endpoint"
 * )
 */
class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryContract $product_repository
    ) {
        $this->middleware('secure')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     operationId="products-list",
     *     summary="List all the products",
     *     tags={"Products"},
     *     security={{}},
     *     @OA\Parameter(ref="#/components/parameters/page_query"),
     *     @OA\Parameter(ref="#/components/parameters/limit_query"),
     *     @OA\Parameter(ref="#/components/parameters/sort_by_query"),
     *     @OA\Parameter(ref="#/components/parameters/desc_query"),
     *     @OA\Parameter(ref="#/components/parameters/category_uuid_query"),
     *     @OA\Parameter(ref="#/components/parameters/price_query"),
     *     @OA\Parameter(ref="#/components/parameters/brand_uuid_query"),
     *     @OA\Parameter(ref="#/components/parameters/title_query"),
     *     @OA\Parameter(ref="#/components/parameters/uuid_query"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function index(ProductListingRequest $request): DefaultCollection
    {
        $products = $this->product_repository->getList();
        return new DefaultCollection($products);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     operationId="products-create",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/StoreProductRequest"
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
    public function store(StoreProductRequest $request): BaseResource
    {
        $this->authorize('create', Product::class);
        $data = $request->validated();
        $data['metadata'] = new ProductMetadata(...$data['metadata']);
        $product = $this->product_repository->create($data);
        return $product ? new BaseResource($product) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{uuid}",
     *     operationId="view-product",
     *     summary="Fetch a product",
     *     tags={"Products"},
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
        $product = $this->product_repository->findByUuid($uuid);
        return $product ? new BaseResource($product) : throw new ModelNotFoundException();
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{uuid}",
     *     operationId="product-edit",
     *     summary="Edit a product",
     *     tags={"Products"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/UpdateProductRequest"
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
    public function update(UpdateProductRequest $request, string $uuid): BaseResource
    {
        $this->authorize('update', Product::class);
        $data = $request->validated();
        $data['metadata'] = new ProductMetadata(...$data['metadata']);
        $product = $this->product_repository->updateByUuid($uuid, $data);
        return $product ? new BaseResource($product) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{uuid}",
     *     operationId="product-delete",
     *     summary="Delete a product",
     *     tags={"Products"},
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
        $this->authorize('delete', Product::class);
        $success = $this->product_repository->deleteByUuid($uuid);
        return $success ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }
}
