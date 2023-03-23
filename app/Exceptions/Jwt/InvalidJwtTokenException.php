<?php

namespace App\Exceptions\Jwt;

use Illuminate\Auth\AuthenticationException;

class InvalidJwtTokenException extends AuthenticationException
{
}
