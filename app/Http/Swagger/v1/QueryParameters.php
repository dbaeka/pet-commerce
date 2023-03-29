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
 *     name="valid",
 *     parameter="valid_query",
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
 *
 * @OA\Parameter(
 *     name="category_uuid",
 *     parameter="category_uuid_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="price",
 *     parameter="price_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="float"),
 * ),
 *
 * @OA\Parameter(
 *     name="brand_uuid",
 *     parameter="brand_uuid_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="title",
 *     parameter="title_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="uuid",
 *     parameter="uuid_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="user_uuid",
 *     parameter="user_uuid_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string"),
 * ),
 *
 * @OA\Parameter(
 *     name="fixed_range",
 *     parameter="fixed_range_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(type="string", enum={"today", "monthly", "yearly"}),
 * ),
 *
 * @OA\Parameter(
 *     name="date_range",
 *     parameter="date_range_query",
 *     in="query",
 *     required=false,
 *     @OA\Schema(
 *      type="object",
 *      required={"from", "to"},
 *      @OA\Property(
 *       property="from",
 *       type="string",
 *       format="date",
 *       description="from date",
 *      ),
 *      @OA\Property(
 *       property="to",
 *       type="string",
 *       format="date",
 *       description="to date",
 *      ),
 *     ),
 * ),
 */
class QueryParameters
{
}
