<?php

namespace App\Services;

use App\Dtos\Token;
use App\Dtos\User;
use App\Exceptions\InvalidPathException;
use App\Exceptions\Jwt\InvalidJwtExpiryException;
use App\Exceptions\Jwt\InvalidJwtIssuerException;
use App\Exceptions\Jwt\InvalidJwtTokenException;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Services\Interfaces\JwtTokenProviderInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\UnencryptedToken;
use Throwable;

class JwtService implements JwtTokenProviderInterface
{
    /**
     * @var non-empty-string string
     */
    private string $issuer;
    private Configuration $config;
    private int $expiry_seconds;
    private readonly JwtTokenRepositoryInterface $jwt_token_repository;


    /**
     * @throws InvalidJwtIssuerException
     * @throws Throwable
     */
    public function __construct()
    {
        $this->jwt_token_repository = app(JwtTokenRepositoryInterface::class);

        $this->loadKeys();

        $this->loadConfig();
    }

    /**
     * @throws InvalidPathException
     */
    private function loadKeys(): void
    {
        $private_key_path = config('jwt.private_key');
        $private_key_passphrase = config('jwt.private_key_passphrase');

        $public_key_path = config('jwt.public_key');
        $public_key_passphrase = config('jwt.public_key_passphrase');
        $this->validatePaths($private_key_path, $public_key_path);


        $private_key = InMemory::file($private_key_path, $private_key_passphrase);
        $public_key = InMemory::file($public_key_path, $public_key_passphrase);
        $this->config = Configuration::forAsymmetricSigner(new Sha256(), $private_key, $public_key);
    }

    /**
     * @throws InvalidPathException
     */
    private function validatePaths(string ...$paths): void
    {
        foreach ($paths as $path) {
            if (!File::exists($path)) {
                throw new InvalidPathException();
            }
        }
    }

    /**
     * @throws InvalidJwtIssuerException
     * @throws Throwable
     */
    private function loadConfig(): void
    {
        $issuer = config('app.url');
        $this->validateIssuer($issuer);

        $this->issuer = $issuer;

        $expiry_seconds = config('jwt.expiry_seconds');
        $this->validateExpiry($expiry_seconds);
        $this->expiry_seconds = intval($expiry_seconds);
    }

    /**
     * @throws InvalidJwtIssuerException|Throwable
     */
    private function validateIssuer(string $issuer): void
    {
        throw_if(empty($issuer), InvalidJwtIssuerException::class);
    }

    /**
     * @throws InvalidJwtExpiryException
     * @throws Throwable
     */
    private function validateExpiry(mixed $expiry_seconds): void
    {
        throw_if($expiry_seconds <= 0 || !is_numeric($expiry_seconds), InvalidJwtExpiryException::class);
    }

    public function generateToken(User $user): Token
    {
        $config = $this->config;

        $now = now();
        $expires_at = now()->addSeconds($this->expiry_seconds);

        $unique_id = hash('sha256', strval(now()->timestamp));

        $jwt = $config->builder()
            ->issuedBy($this->issuer)
            ->identifiedBy($unique_id)
            ->withClaim('user_uuid', $user->uuid)
            ->issuedAt($now->toDateTimeImmutable())
            ->expiresAt($expires_at->toDateTimeImmutable())
            ->getToken($config->signer(), $config->signingKey());

        $data = [
            'user_id' => $user->getId(),
            'unique_id' => $unique_id,
            'token_title' => 'Access Token',
            'expires_at' => $expires_at,
        ];
        return Token::make($data)->withToken($jwt->toString());
    }

    /**
     * @throws InvalidJwtTokenException
     */
    public function getPayload(string $token): array
    {
        $parsed_token = $this->parseToken($token);
        if (empty($parsed_token)) {
            throw new InvalidJwtTokenException();
        }
        return $parsed_token->claims()->all();
    }

    private function parseToken(string $token): ?UnencryptedToken
    {
        if (empty($token)) {
            return null;
        }
        $config = $this->config;
        try {
            $jwt = $config->parser()->parse($token);
            assert($jwt instanceof UnencryptedToken);
            return $jwt;
        } catch (Throwable $e) {
            Log::error('error parsing token: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * @throws InvalidJwtTokenException
     */
    public function getUserFromToken(string $token): ?User
    {
        $parsed_token = $this->parseToken($token);
        if (empty($parsed_token)) {
            throw new InvalidJwtTokenException();
        }

        $unique_id = $parsed_token->claims()->get('jti');
        return $this->jwt_token_repository->getUserByUniqueId($unique_id);
    }

    public function validateToken(string $token): bool
    {
        $user = app(JwtTokenProviderInterface::class)->authenticate($token);
        return !empty($user);
    }

    /**
     * @throws InvalidJwtTokenException
     */
    public function authenticate(string $token): ?User
    {
        $parsed_token = $this->parseToken($token);
        if (empty($parsed_token)) {
            throw new InvalidJwtTokenException();
        }

        $unique_id = $parsed_token->claims()->get('jti');
        $jwt_token_exists = $this->jwt_token_repository->checkTokenExists($unique_id);

        if ($jwt_token_exists) {
            // TODO change to event
            $this->jwt_token_repository->updateTokenLastUsed($unique_id);
        }

        $is_expired = $parsed_token->isExpired(now());
        if ($jwt_token_exists && !$is_expired) {
            return $this->jwt_token_repository->getUserByUniqueId($unique_id);
        }
        if ($is_expired) {
            $this->jwt_token_repository->deleteToken($unique_id);
        }
        return null;
    }

    /**
     * @throws InvalidJwtTokenException
     */
    public function inValidateToken(string $token): bool
    {
        $parsed_token = $this->parseToken($token);
        if (empty($parsed_token)) {
            throw new InvalidJwtTokenException();
        }

        $unique_id = $parsed_token->claims()->get('jti');
        return $this->jwt_token_repository->deleteToken($unique_id);
    }
}
