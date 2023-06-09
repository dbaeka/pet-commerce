<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\OrderStatus\OrderStatusListingRequest;
use App\Http\Requests\v1\OrderStatus\StoreOrderStatusRequest;
use App\Http\Requests\v1\OrderStatus\UpdateOrderStatusRequest;
use App\Http\Resources\v1\BaseResource;
use App\Http\Resources\v1\DefaultCollection;
use App\Models\OrderStatus;
use App\Repositories\Interfaces\OrderStatusRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Order Statuses",
 *     description="Order Statuses API endpoint"
 * )
 */
class OrderStatusController extends Controller
{
    public function __construct(
        private readonly OrderStatusRepositoryContract $order_status_repository
    ) {
        $this->middleware('secure')->except(['index', 'show']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/order-statuses",
     *     operationId="order-statuses-list",
     *     summary="List all the order statuses",
     *     tags={"Order Statuses"},
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
    public function index(OrderStatusListingRequest $request): DefaultCollection
    {
        $order_statuses = $this->order_status_repository->getList();
        return new DefaultCollection($order_statuses);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/order-statuses",
     *     operationId="order-status-create",
     *     summary="Create a new order status",
     *     tags={"Order Statuses"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/StoreOrderStatusRequest"
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
    public function store(StoreOrderStatusRequest $request): JsonResponse
    {
        $this->authorize('create', OrderStatus::class);
        $data = \App\DataObjects\OrderStatus::from($request->validated());
        $order_status = $this->order_status_repository->create($data);
        return $order_status ? (new BaseResource($order_status))->response()->setStatusCode(SResponse::HTTP_CREATED) :
            throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/order-statuses/{uuid}",
     *     operationId="view-order-status",
     *     summary="Fetch an order status",
     *     tags={"Order Statuses"},
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
        $order_status = $this->order_status_repository->findByUuid($uuid);
        return $order_status ? new BaseResource($order_status) : throw new ModelNotFoundException();
    }

    /**
     * @OA\Put(
     *     path="/api/v1/order-statuses/{uuid}",
     *     operationId="order-status-edit",
     *     summary="Edit an order status",
     *     tags={"Order Statuses"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/UpdateOrderStatusRequest"
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
    public function update(UpdateOrderStatusRequest $request, string $uuid): BaseResource
    {
        $this->authorize('update', OrderStatus::class);
        $data = \App\DataObjects\OrderStatus::from($request->validated());
        $order_status = $this->order_status_repository->updateByUuid($uuid, $data);
        return $order_status ? new BaseResource($order_status) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/order-statuses/{uuid}",
     *     operationId="order-status-delete",
     *     summary="Delete an order status",
     *     tags={"Order Statuses"},
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
        $this->authorize('delete', OrderStatus::class);
        $success = $this->order_status_repository->deleteByUuid($uuid);
        return $success ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }
}
