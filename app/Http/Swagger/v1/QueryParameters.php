<?php

namespace App\Http\Swagger\v1;

/**
 * @OA\Parameter(
 *     name="page",
 *     parameter="page_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="integer"),
 * ),
 *
 * @OA\Parameter(
 *     name="limit",
 *     parameter="limit_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="integer"),
 * ),
 *
 * @OA\Parameter(
 *     name="sortBy",
 *     parameter="sort_by_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="desc",
 *     parameter="desc_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="boolean"),
 * ),
 *
 * @OA\Parameter(
 *     name="first_name",
 *     parameter="first_name_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="email",
 *     parameter="email_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="phone",
 *     parameter="phone_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="address",
 *     parameter="address_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="created_at",
 *     parameter="created_at_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="marketing",
 *     parameter="marketing_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", enum={"1", "2"}),
 * ),
 */
class QueryParameters
{
}
