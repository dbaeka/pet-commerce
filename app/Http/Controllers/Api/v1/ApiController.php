<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      x={
 *          "logo": {
 *              "url": "https://via.placeholder.com/190x90.png?text=L5-Swagger"
 *          }
 *      },
 *      title="Pet Shop API - Swagger Documentation",
 *      description="This API has been created for Buckhill's Pet Commerce",
 *      @OA\Contact(email="dbaekajnr@gmail.com")
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 *
 * @OA\OpenApi(
 *     security={
 *       {"apiKeyAuth": {}}
 *     }
 * )
 */
class ApiController extends Controller
{
}
