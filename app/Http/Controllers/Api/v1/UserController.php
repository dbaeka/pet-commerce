<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Order\OrderListingRequest;
use App\Http\Requests\v1\User\ForgotPasswordRequest;
use App\Http\Requests\v1\User\ResetPasswordTokenRequest;
use App\Http\Requests\v1\User\UserCreateRequest;
use App\Http\Requests\v1\User\UserEditRequest;
use App\Http\Requests\v1\User\UserLoginRequest;
use App\Http\Resources\v1\DefaultCollection;
use App\Http\Resources\v1\ForgotPasswordResource;
use App\Http\Resources\v1\LoginResource;
use App\Http\Resources\v1\MessageResource;
use App\Http\Resources\v1\UserCreateResource;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use App\Services\Auth\ForgotPassword;
use App\Services\Auth\LoginUserWithCreds;
use App\Services\Auth\LogoutUser;
use App\Services\Auth\ResetPassword;
use App\Services\User\CreateRegularUser;
use App\Services\User\UpdateUserDetails;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="User",
 *     description="User API endpoint"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryContract  $user_repository,
        private readonly OrderRepositoryContract $order_repository
    ) {
        $this->middleware('secure')->except([
            'login', 'store', 'forgotPassword', 'resetPasswordToken'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     operationId="view-user",
     *     summary="View a User Account",
     *     tags={"User"},
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(): UserResource
    {
        $this->authorize('view', User::class);
        /** @var User $user */
        $user = Auth::user();
        $user_dto = $this->user_repository->findByUuid($user->uuid);
        return $user_dto ? new UserResource($user_dto) : throw new ModelNotFoundException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/orders",
     *     operationId="user-orders",
     *     summary="List all orders for the user",
     *     tags={"User"},
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
    public function getOrders(OrderListingRequest $request): DefaultCollection
    {
        $this->authorize('getOrders', User::class);
        /** @var User $user */
        $user = Auth::user();
        $orders = $this->order_repository->getUserOrders($user->uuid);
        return new DefaultCollection($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     operationId="user-create",
     *     summary="Create an User account",
     *     tags={"User"},
     *     security={{}},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/UserCreateRequest"
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
    public function store(UserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = app(CreateRegularUser::class)->execute($data);
        return $user ? (new UserCreateResource((object)$user->toArray()))->response()->setStatusCode(201) :
            throw new UnprocessableEntityHttpException();
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user/reset-password-token",
     *     operationId="user-reset-password-token",
     *     summary="Reset a user password with the token",
     *     tags={"User"},
     *     security={{}},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/ResetPasswordTokenRequest"
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
    public function resetPasswordToken(ResetPasswordTokenRequest $request): MessageResource
    {
        $data = $request->validated();
        $success = app(ResetPassword::class)->execute($data);
        return $success ? new MessageResource('Password has been successfully updated') :
            throw new UnprocessableEntityHttpException();
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user/forgot-password",
     *     operationId="user-forgot-password",
     *     summary="Create a token to reset a User password",
     *     tags={"User"},
     *     security={{}},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/ForgotPasswordRequest"
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
    public function forgotPassword(ForgotPasswordRequest $request): ForgotPasswordResource
    {
        $data = $request->validated();
        $token = app(ForgotPassword::class)->execute($data['email']);
        return new ForgotPasswordResource($token);
    }


    /**
     * @OA\Put(
     *     path="/api/v1/user/edit",
     *     operationId="user-edit",
     *     summary="Edit a User account",
     *     tags={"User"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/UserEditRequest"
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
    public function update(UserEditRequest $request): UserResource
    {
        /** @var User $user */
        $user = Auth::user();
        $data = $request->validated();
        $updated_user = app(UpdateUserDetails::class)->execute($user->uuid, $data);
        return $updated_user ? new UserResource($user) : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/user",
     *     operationId="user-delete",
     *     summary="Delete a User account",
     *     tags={"User"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=204, ref="#/components/responses/NoContent"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function delete(): Response
    {
        $this->authorize('delete', User::class);
        /** @var User $user */
        $user = Auth::user();
        $deleted = $this->user_repository->deleteByUuid($user->uuid);
        return $deleted ? response()->noContent() : throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     operationId="user-login",
     *     summary="Login a User account",
     *     tags={"User"},
     *     security={{}},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *        ref="#/components/schemas/UserLoginRequest"
     *       )
     *      )
     *     ),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     * @throws AuthenticationException
     */
    public function login(UserLoginRequest $request): LoginResource
    {
        $credentials = $request->validated();
        $token = app(LoginUserWithCreds::class)->execute($credentials);
        return $token ? new LoginResource($token) : throw new AuthenticationException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/logout",
     *     operationId="user-logout",
     *     summary="Logout a User account",
     *     tags={"User"},
     *     @OA\Response(response=204, ref="#/components/responses/NoContent"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function logout(): Response
    {
        $this->authorize('logout', User::class);
        if (app(LogoutUser::class)->execute()) {
            return response()->noContent();
        }
        throw new UnprocessableEntityHttpException();
    }
}
