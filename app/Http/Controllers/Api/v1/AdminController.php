<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AdminLoginRequest;
use App\Http\Requests\v1\AdminUserCreateRequest;
use App\Http\Requests\v1\AdminUserEditRequest;
use App\Http\Requests\v1\AdminUserListingRequest;
use App\Http\Resources\v1\BaseCollection;
use App\Http\Resources\v1\LoginResource;
use App\Http\Resources\v1\UserCreateResource;
use App\Http\Resources\v1\UserResource;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin API endpoint"
 * )
 */
class AdminController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $user_repository
    ) {
        $this->middleware('secure:admin')->except([
            'login',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/user-listing",
     *     operationId="admin-user-listing",
     *     summary="List all users",
     *     tags={"Admin"},
     *     @OA\Parameter(ref="#/components/parameters/page_query"),
     *     @OA\Parameter(ref="#/components/parameters/limit_query"),
     *     @OA\Parameter(ref="#/components/parameters/sort_by_query"),
     *     @OA\Parameter(ref="#/components/parameters/desc_query"),
     *     @OA\Parameter(ref="#/components/parameters/first_name_query"),
     *     @OA\Parameter(ref="#/components/parameters/email_query"),
     *     @OA\Parameter(ref="#/components/parameters/phone_query"),
     *     @OA\Parameter(ref="#/components/parameters/address_query"),
     *     @OA\Parameter(ref="#/components/parameters/created_at_query"),
     *     @OA\Parameter(ref="#/components/parameters/marketing_query"),
     *     @OA\Response(response=200, ref="#/components/responses/OK"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function getUserListing(AdminUserListingRequest $request): BaseCollection
    {
        $users = $this->user_repository->getNonAdminUsers();
        return new BaseCollection($users);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/create",
     *     operationId="admin-create",
     *     summary="Create an Admin account",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/AdminUserCreateRequest"
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
    public function store(AdminUserCreateRequest $request): UserCreateResource
    {
        $data = $request->validated();
        $user = (new UserService())->createAdmin($data);
        if ($user) {
            return new UserCreateResource((object)$user);
        }
        throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Put(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     operationId="admin-user-edit",
     *     summary="Edit a User account",
     *     tags={"Admin"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/AdminUserEditRequest"
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
    public function editUser(AdminUserEditRequest $request, string $uuid): UserResource
    {
        $data = $request->validated();
        $user = $this->user_repository->findUserByUuid($uuid);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }
        $updated_user = $this->user_repository->editUserByUuid(
            $uuid,
            Arr::except($data, ['password_confirmation'])
        );

        if ($updated_user) {
            return new UserResource($user);
        }
        throw new UnprocessableEntityHttpException();
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/user-delete/{uuid}",
     *     operationId="admin-user-delete",
     *     summary="Delete a User account",
     *     tags={"Admin"},
     *     @OA\Parameter(ref="#/components/parameters/uuid_path"),
     *     @OA\Response(response=204, ref="#/components/responses/NoContent"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function deleteUser(string $uuid): Response
    {
        $user = $this->user_repository->findUserByUuid($uuid);
        if ($user) {
            $deleted = $this->user_repository->deleteUserByUuid($uuid);
            return $deleted ? response()->noContent() : throw new UnprocessableEntityHttpException();
        }
        throw new ModelNotFoundException();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     operationId="admin-login",
     *     summary="Login an Admin account",
     *     tags={"Admin"},
     *     security={{}},
     *     @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *       mediaType="application/x-www-form-urlencoded",
     *       @OA\Schema(
     *        ref="#/components/schemas/AdminLoginRequest"
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
    public function login(AdminLoginRequest $request): LoginResource
    {
        $credentials = $request->validated();
        $token = (new AuthService())->loginAdminUser($credentials);
        if ($token) {
            return new LoginResource($token);
        }
        throw new AuthenticationException();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/logout",
     *     operationId="admin-logout",
     *     summary="Logout an Admin account",
     *     tags={"Admin"},
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
