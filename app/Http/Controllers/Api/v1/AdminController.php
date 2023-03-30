<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\AdminLoginRequest;
use App\Http\Requests\v1\Admin\AdminUserCreateRequest;
use App\Http\Requests\v1\Admin\AdminUserEditRequest;
use App\Http\Requests\v1\Admin\AdminUserListingRequest;
use App\Http\Resources\v1\DefaultCollection;
use App\Http\Resources\v1\LoginResource;
use App\Http\Resources\v1\UserCreateResource;
use App\Http\Resources\v1\UserResource;
use App\Repositories\Interfaces\UserRepositoryContract;
use App\Services\Auth\LoginAdminWithCreds;
use App\Services\Auth\LogoutUser;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
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
        private readonly UserRepositoryContract $user_repository
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
    public function getUserListing(AdminUserListingRequest $request): DefaultCollection
    {
        $users = $this->user_repository->getNonAdminUsers();
        return new DefaultCollection($users);
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
     *     @OA\Response(response=201, ref="#/components/responses/Created"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function store(AdminUserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = (new UserService())->createAdmin($data);
        if ($user) {
            return (new UserCreateResource((object)$user))->response()->setStatusCode(201);
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
        $user = $this->user_repository->findByUuid($uuid);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }
        $updated_user = $this->user_repository->updateByUuid(
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
        $deleted = $this->user_repository->deleteByUuid($uuid);
        return $deleted ? response()->noContent() : throw new UnprocessableEntityHttpException();
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
        $token = app(LoginAdminWithCreds::class)->execute($credentials);
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
        if (app(LogoutUser::class)->execute()) {
            return response()->noContent();
        }
        throw new UnprocessableEntityHttpException();
    }
}
