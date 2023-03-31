<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Payment\PaymentListingRequest;
use App\Http\Requests\v1\Payment\StorePaymentRequest;
use App\Http\Requests\v1\Payment\UpdatePaymentRequest;
use App\Http\Resources\v1\BaseResource;
use App\Http\Resources\v1\DefaultCollection;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\Interfaces\PaymentRepositoryContract;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="Payments API endpoint"
 * )
 */
class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentRepositoryContract $payment_repository
    ) {
        $this->middleware('secure');
        $this->authorizeResource(Payment::class);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments",
     *     operationId="payments-list",
     *     summary="List all the payments for admin and user payments if not admin",
     *     tags={"Payments"},
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
    public function index(PaymentListingRequest $request): DefaultCollection
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->is_admin) {
            $payments = $this->payment_repository->getList();
        } else {
            $payments = $this->payment_repository->getListForUserUuid($user->uuid);
        }
        return new DefaultCollection($payments);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/payments",
     *     operationId="payments-create",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/StorePaymentRequest"
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
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $data = \App\DataObjects\Payment::from($request->validated());
        $payment = $this->payment_repository->create($data);
        return $payment ? (new BaseResource($payment))->response()->setStatusCode(SResponse::HTTP_CREATED) :
            throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/payments/{uuid}",
     *     operationId="view-payment",
     *     summary="Fetch a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(Payment $payment): BaseResource
    {
        return new BaseResource($payment);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/payments/{uuid}",
     *     operationId="payment-edit",
     *     summary="Edit a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/UpdatePaymentRequest"
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
    public function update(UpdatePaymentRequest $request, Payment $payment): BaseResource
    {
        $data = \App\DataObjects\Payment::from($request->validated());
        $payment = $this->payment_repository->updateByUuid($payment->uuid, $data);
        return $payment ? new BaseResource($payment) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/payments/{uuid}",
     *     operationId="payment-delete",
     *     summary="Delete a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=204, ref="#/components/responses/NoContent"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function destroy(Payment $payment): Response
    {
        $success = $this->payment_repository->deleteByUuid($payment->uuid);
        return $success ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }
}
