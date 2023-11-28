<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   schema="News",
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     example="1"
 *   ),
 *   @OA\Property(
 *     property="source_id",
 *     type="string",
 *     example="550e8400-e29b-41d4-a716-446655440000"
 *   ),
 *   @OA\Property(
 *     property="summary",
 *     type="string",
 *     format="text",
 *   ),
 *   @OA\Property(
 *     property="body",
 *     type="string",
 *     format="LongText",
 *   ),
 *   @OA\Property(
 *     property="image",
 *     type="string",
 *     description="url to the image",
 *   ),
 *   @OA\Property(
 *     property="author",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="source",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="section_name",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="source_url",
 *     type="string",
 *   ),
 *   @OA\Property(
 *     property="published_at",
 *     type="datetime",
 *   ),
 * )
 */
class News extends Model
{
    use HasFactory;

    public static $sortables = [
        "published_at",
        "created_at",
        "updated_at"
    ];

    public static $searchWithQueryString = [
        "summary", // index 0 is always the default
        "title",
        "body",
    ];

    protected $casts = [
        "published_at" => 'datetime'
    ];

    protected $hidden = [
        "source_id",
        "created_at",
        "updated_at",
        "body",
    ];
}
