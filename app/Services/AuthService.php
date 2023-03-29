<?php

namespace App\Services;

use App\Dtos\User;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\Interfaces\ResetRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Interfaces\JwtTokenProviderInterface;
use Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class AuthService
{
    private JwtTokenProviderInterface $jwt_service;
    private JwtTokenRepositoryInterface $jwt_token_repository;
    private UserRepository $user_repository;
    private ResetRepositoryInterface $reset_repository;

    public function __construct()
    {
        $this->jwt_service = app(JwtTokenProviderInterface::class);
        $this->jwt_token_repository = app(JwtTokenRepositoryInterface::class);
        $this->user_repository = app(UserRepository::class);
        $this->reset_repository = app(ResetRepositoryInterface::class);
    }

    /**
     * @param array<string, mixed> $credentials
     * @return string|null
     */
    public function loginAdminUser(array $credentials): ?string
    {
        if (Auth::attempt($credentials)) {
            $user = $this->userDtoFromModel();
            if ($user->getIsAdmin()) {
                return $this->generateToken($user);
            }
        }
        return null;
    }

    private function userDtoFromModel(): User
    {
        /** @var array<string, mixed> $user_data */
        $user_data = Auth::user()?->getAttributes();
        return User::make($user_data);
    }

    private function generateToken(User $user): ?string
    {
        $token = $this->jwt_service->generateToken($user);
        $token_id = $this->jwt_token_repository->createToken($token);
        if (!empty($token_id)) {
            // TODO change to event
            $this->user_repository->updateLastLogin($user->uuid);
            return $token->getTokenValue();
        }
        return null;
    }

    /**
     * @param array<string, mixed> $credentials
     * @return string|null
     */
    public function loginRegularUser(array $credentials): ?string
    {
        if (Auth::attempt($credentials)) {
            $user = $this->userDtoFromModel();
            if (!$user->getIsAdmin()) {
                return $this->generateToken($user);
            }
        }
        return null;
    }

    public function loginUsingId(int $id): ?string
    {
        /** @var \App\Models\User|false $user */
        $user = Auth::onceUsingId($id);
        if ($user) {
            $user_dto = User::make($user->getAttributes());
            $token = $this->jwt_service->generateToken($user_dto);
            $token_id = $this->jwt_token_repository->createToken($token);
            if (!empty($token_id)) {
                // TODO change to event
                $this->user_repository->updateLastLogin($user->uuid);
                return $token->getTokenValue();
            }
        }
        return null;
    }


    public function logoutUser(): bool
    {
        Auth::logout();
        $token = request()->bearerToken();
        if ($token) {
            return $this->jwt_service->inValidateToken($token);
        }
        return true;
    }

    public function forgotPassword(string $email): string
    {
        $user = $this->user_repository->findUserByEmail($email);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }

        if ($user->getIsAdmin()) {
            throw new UnauthorizedException();
        }

        $token = hash('sha256', strval(now()->timestamp));

        $saved = $this->reset_repository->addResetToken($email, $token);
        if ($saved) {
            return $token;
        }
        throw new UnprocessableEntityHttpException();
    }

    /**
     * @param array<string, mixed> $data
     * @return bool
     */
    public function resetPassword(array $data): bool
    {
        $user = $this->user_repository->findUserByEmail($data['email']);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }

        if ($user->getIsAdmin()) {
            throw new UnauthorizedException();
        }
        $data['password'] = Hash::make($data['password']);
        $user = $this->user_repository->updateByUuid($user->uuid, Arr::only($data, ['password']));
        $success = $this->reset_repository->deleteToken($data['email']);
        if ($user && $success) {
            return true;
        }
        return false;
    }
}
