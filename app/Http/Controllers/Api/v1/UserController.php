<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ForgotPasswordRequest;
use App\Http\Requests\v1\OrderListingRequest;
use App\Http\Requests\v1\ResetPasswordTokenRequest;
use App\Http\Requests\v1\UserCreateRequest;
use App\Http\Requests\v1\UserEditRequest;
use App\Http\Requests\v1\UserLoginRequest;
use App\Http\Resources\v1\BaseCollection;
use App\Http\Resources\v1\LoginResource;
use App\Http\Resources\v1\ForgotPasswordResource;
use App\Http\Resources\v1\MessageResource;
use App\Http\Resources\v1\UserCreateResource;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
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
        private readonly UserRepositoryInterface  $user_repository,
        private readonly OrderRepositoryInterface $order_repository
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
        /** @var User $user */
        $user = Auth::user();
        $user_dto = $this->user_repository->findUserByUuid($user->uuid);
        if ($user_dto) {
            return new UserResource($user_dto);
        }
        throw new ModelNotFoundException();
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
    public function getOrders(OrderListingRequest $request): BaseCollection
    {
        /** @var User $user */
        $user = Auth::user();
        $orders = $this->order_repository->getUserOrders($user->uuid);
        return new BaseCollection($orders);
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
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/UserCreateRequest"
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
    public function store(UserCreateRequest $request): UserCreateResource
    {
        $data = $request->validated();
        $user = (new UserService())->createUser($data);
        if ($user) {
            return new UserCreateResource((object)$user);
        }
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
     *       mediaType="application/x-www-form-urlencoded",
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
        $success = (new AuthService())->resetPassword($data);
        if ($success) {
            return new MessageResource('Password has been successfully updated');
        }
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
     *       mediaType="application/x-www-form-urlencoded",
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
        $token = (new AuthService())->forgotPassword($data['email']);
        return new ForgotPasswordResource($token);
    }


    /**
     * @OA\Put(
     *     path="/api/v1/user/edit",
     *     operationId="user-edit",
     *     summary="Edit a User account",
     *     tags={"User"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
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
        $data = $request->validated();
        /** @var User $user */
        $user = Auth::user();
        $user_dto = $this->user_repository->findUserByUuid($user->uuid);
        if (empty($user_dto)) {
            throw new ModelNotFoundException();
        }
        $updated_user = $this->user_repository->editUserByUuid(
            $user_dto->uuid,
            Arr::except($data, ['password_confirmation'])
        );

        if ($updated_user) {
            return new UserResource($user);
        }
        throw new UnprocessableEntityHttpException();
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
        /** @var User $user */
        $user = Auth::user();
        $user_dto = $this->user_repository->findUserByUuid($user->uuid);
        if ($user_dto) {
            $deleted = $this->user_repository->deleteUserByUuid($user->uuid);
            return $deleted ? response()->noContent() : throw new UnprocessableEntityHttpException();
        }
        throw new ModelNotFoundException();
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
     *       mediaType="application/x-www-form-urlencoded",
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
        $token = (new AuthService())->loginRegularUser($credentials);
        if ($token) {
            return new LoginResource($token);
        }
        throw new AuthenticationException();
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
        if ((new AuthService())->logoutUser()) {
            return response()->noContent();
        }
        throw new UnprocessableEntityHttpException();
    }
}
