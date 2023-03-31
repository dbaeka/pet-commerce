<?php

namespace App\Services\Jwt;

use App\Exceptions\InvalidPath;
use App\Exceptions\Jwt\InvalidJwtExpiry;
use App\Exceptions\Jwt\InvalidJwtIssuer;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\UnencryptedToken;
use Throwable;

abstract class BaseJwtProvider
{
    /**
     * @var non-empty-string string
     */
    protected string $issuer;
    protected Configuration $config;
    protected int $expiry_seconds;
    protected readonly JwtTokenRepositoryContract $jwt_token_repository;

    /**
     * @throws InvalidJwtIssuer
     * @throws Throwable
     */
    public function __construct()
    {
        $this->jwt_token_repository = app(JwtTokenRepositoryContract::class);

        $this->loadKeys();

        $this->loadConfig();
    }

    /**
     * @throws InvalidPath
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
     * @throws InvalidPath
     */
    private function validatePaths(string ...$paths): void
    {
        foreach ($paths as $path) {
            if (!File::exists($path)) {
                throw new InvalidPath();
            }
        }
    }

    /**
     * @throws InvalidJwtIssuer
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
     * @throws InvalidJwtIssuer|Throwable
     */
    private function validateIssuer(string $issuer): void
    {
        throw_if(empty($issuer), InvalidJwtIssuer::class);
    }

    /**
     * @throws InvalidJwtExpiry
     * @throws Throwable
     */
    private function validateExpiry(mixed $expiry_seconds): void
    {
        throw_if($expiry_seconds <= 0 || !is_numeric($expiry_seconds), InvalidJwtExpiry::class);
    }

    protected function parseToken(string $token): ?UnencryptedToken
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
}
