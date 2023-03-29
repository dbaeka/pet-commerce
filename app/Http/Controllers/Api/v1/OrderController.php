<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Order\OrderListingRequest;
use App\Http\Requests\v1\Order\StoreOrderRequest;
use App\Http\Requests\v1\Order\UpdateOrderRequest;
use App\Http\Resources\v1\BaseResource;
use App\Http\Resources\v1\DefaultCollection;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Orders API endpoint"
 * )
 */
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepository $order_repository
    ) {
        $this->middleware('secure:admin')->only(['getDashboard', 'getShipmentLocator']);
        $this->middleware('secure')->except(['getDashboard', 'getShipmentLocator']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     operationId="orders-list",
     *     summary="List all the orders",
     *     tags={"Orders"},
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
    public function index(OrderListingRequest $request): DefaultCollection
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->is_admin) {
            $orders = $this->order_repository->getList();
        } else {
            $orders = $this->order_repository->getListForUserUuid($user->uuid);
        }
        return new DefaultCollection($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     operationId="orders-create",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/StoreOrderRequest"
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
    public function store(StoreOrderRequest $request): BaseResource
    {
        $this->authorize('create', Order::class);
        $data = $request->validated();
        $order = (new OrderService())->createOrder($data);
        return $order ? new BaseResource($order) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{uuid}",
     *     operationId="view-order",
     *     summary="Fetch an order",
     *     tags={"Orders"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(Order $order): BaseResource
    {
        $this->authorize('view', [Order::class, $order]);
        $order = $this->order_repository->findByUuid($order->uuid);
        return $order ? new BaseResource($order) : throw new ModelNotFoundException();
    }

    /**
     * @OA\Put(
     *     path="/api/v1/orders/{uuid}",
     *     operationId="order-edit",
     *     summary="Edit an order",
     *     tags={"Orders"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/UpdateOrderRequest"
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
    public function update(UpdateOrderRequest $request, Order $order): BaseResource
    {
        $this->authorize('update', [Order::class, $order]);
        $data = $request->validated();
        $order = (new OrderService())->updateOrder($order->uuid, $data);
        return $order ? new BaseResource($order) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{uuid}",
     *     operationId="order-delete",
     *     summary="Delete an order",
     *     tags={"Orders"},
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
        $this->authorize('delete', Order::class);
        $success = $this->order_repository->deleteByUuid($uuid);
        return $success ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }
}