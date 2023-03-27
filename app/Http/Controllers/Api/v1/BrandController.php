<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Brand\StoreBrandRequest;
use App\Http\Requests\v1\Brand\UpdateBrandRequest;
use App\Http\Resources\v1\DefaultCollection;
use App\Http\Resources\v1\BaseResource;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Brands",
 *     description="Brands API endpoint"
 * )
 */
class BrandController extends Controller
{
    public function __construct(
        private readonly BrandRepository $brand_repository
    ) {
        $this->middleware('secure')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brands",
     *     operationId="brands-list",
     *     summary="List all the brands",
     *     tags={"Brands"},
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
    public function index(): DefaultCollection
    {
        $brands = $this->brand_repository->getList();
        return new DefaultCollection($brands);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/brands",
     *     operationId="brands-create",
     *     summary="Create a new brand",
     *     tags={"Brands"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/StoreBrandRequest"
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
    public function store(StoreBrandRequest $request): BaseResource
    {
        $this->authorize('create', Brand::class);
        $data = $request->validated();
        $brand = $this->brand_repository->create($data);
        return $brand ? new BaseResource($brand) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/brands/{uuid}",
     *     operationId="view-brand",
     *     summary="Fetch a brand",
     *     tags={"Brands"},
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
        $brand = $this->brand_repository->findByUuid($uuid);
        return $brand ? new BaseResource($brand) : throw new ModelNotFoundException();
    }

    /**
     * @OA\Put(
     *     path="/api/v1/brands/{uuid}",
     *     operationId="brand-edit",
     *     summary="Edit a brand",
     *     tags={"Brands"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/UpdateBrandRequest"
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
    public function update(UpdateBrandRequest $request, string $uuid): BaseResource
    {
        $this->authorize('update', Brand::class);
        $data = $request->validated();
        $brand = $this->brand_repository->updateByUuid($uuid, $data);
        return $brand ? new BaseResource($brand) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/brands/{uuid}",
     *     operationId="brand-delete",
     *     summary="Delete a brand",
     *     tags={"Brands"},
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
        $this->authorize('delete', Brand::class);
        $success = $this->brand_repository->deleteByUuid($uuid);
        return $success ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }
}
