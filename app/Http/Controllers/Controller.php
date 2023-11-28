<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="News Aggregator Api Documentation",
 *     @OA\Contact(
 *         name="Mahdi Abbariki",
 *         email="mahdi.abbariki0@gmail.com"
 *     ),
 * ),
 * @OA\Server(
 *     url="/api",
 * ),
 * @OA\Schema(
 *  schema="ValidationErrorResponse",
 *  @OA\Property(
 *      property="message",
 *      type="string",
 *      example="The propertyName is required. (and 1 more error)",
 *      description="the first error message and it also contains the count of other errors occurred"
 *      ),
 *  @OA\Property(
 *      property="errors", 
 *      type="object",
 *      @OA\Property(
 *          property="propertyName", 
 *          type="array", 
 *          collectionFormat="multi",
 *          @OA\Items(
 *              type="string",
 *              example="The propertyName is required.",
 *          )
 *      )
 *   )
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
