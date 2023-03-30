<?php

namespace App\Services\Jwt;

use App\Exceptions\InvalidPathException;
use App\Exceptions\Jwt\InvalidJwtExpiryException;
use App\Exceptions\Jwt\InvalidJwtIssuerException;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
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
    protected readonly JwtTokenRepositoryInterface $jwt_token_repository;


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
