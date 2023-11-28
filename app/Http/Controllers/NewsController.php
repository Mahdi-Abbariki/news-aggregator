<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsIndexRequest;
use App\Models\News;
use Carbon\Carbon;

class NewsController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"News"},
     *   path="/news",
     *   summary="News index",
     *   @OA\Parameter(
     *    name="from", 
     *    in="query", 
     *    description="of type ISO8601",
     *    @OA\Schema(
     *      type="datetime",
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="to", 
     *    in="query", 
     *    description="of type ISO8601",
     *    @OA\Schema(
     *      type="datetime",
     *      nullable=true,
     *    )
     *  ),
     *   @OA\Parameter(
     *    name="sortBy", 
     *    in="query", 
     *    description="required with sortType",
     *    @OA\Schema(
     *      type="string",
     *      enum={"published_at","created_at","updated_at"},
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="sortType", 
     *    in="query", 
     *    description="required with sortBy",
     *    @OA\Schema(
     *      type="string",
     *      enum={"ASC","DESC"},
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="title", 
     *    in="query",
     *    description="search in title of news with LIKE operator (%title%)",
     *    @OA\Schema(
     *      type="string",
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="author", 
     *    in="query",
     *    description="search in author of news with LIKE operator (%author%)",
     *    @OA\Schema(
     *      type="string",
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="section_name", 
     *    in="query", 
     *    description="search in section_name of news (exact match). use /sections api to get full list of sources",
     *    @OA\Schema(
     *      type="string",
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="source", 
     *    in="query", 
     *    description="search in source of news (exact match). use /sources api to get full list of sources",
     *    @OA\Schema(
     *      type="string",
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="q", 
     *    in="query", 
     *    description="query string to search in multiple data at once. required if search_in is specified",
     *    @OA\Schema(
     *      type="string",
     *      nullable=true,
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="search_in[]", 
     *    in="query",
     *    description="choose the columns to search `q` in them. they will be search as `OR` and it uses LIKE operator. possible values `summary`, `title` and `body`",
     *    @OA\Schema(
     *      type="array",
     *      @OA\Items(
     *        type="string",
     *      )
     *    )
     *  ),
     *  @OA\Parameter(
     *    name="include_body",
     *    in="query",
     *    description="append body of news or not",
     *    @OA\Schema(type="boolean", default=false)
     *  ),
     *  @OA\Parameter(
     *    name="page",
     *    in="query",
     *    description="Page number for pagination",
     *    @OA\Schema(type="integer", default="1")
     *  ),
     *  @OA\Parameter(
     *    name="per_page",
     *    in="query",
     *    description="the number of news included in result. 10 is the default. maximum is 50",
     *    @OA\Schema(type="integer", default=10)
     *  ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/News")),
     *         @OA\Property(property="first_page_url", type="string"),
     *         @OA\Property(property="last_page_url", type="string"),
     *         @OA\Property(property="next_page_url", type="string"),
     *         @OA\Property(property="prev_page_url", type="string"),
     *         @OA\Property(property="path", type="string"),
     *         @OA\Property(property="current_page", type="integer"),
     *         @OA\Property(property="from", type="integer"),
     *         @OA\Property(property="to", type="integer"),
     *         @OA\Property(property="per_page", type="integer"),
     *         @OA\Property(property="total", type="integer"),
     *         @OA\Property(property="last_page", type="integer"),
     *     )
     *   )
     * )
     */
    public function index(NewsIndexRequest $request)
    {
        $news = News::query();

        if ($request->sortBy && $request->sortType)
            $news = $news->orderBy($request->sortBy, $request->sortType);
        else
            $news = $news->latest('published_at');


        if ($request->from)
            $news = $news->where('published_at', '>=', Carbon::parse($request->from)->format('Y-m-d H:i:s'));

        if ($request->to)
            $news = $news->where('published_at', '<=', Carbon::parse($request->to)->format('Y-m-d H:i:s'));

        if ($request->title)
            $news = $news->where('title', 'LIKE', "%$request->title%");

        if ($request->author)
            $news = $news->where('author', 'LIKE', "%$request->author%");

        if ($request->section_name)
            $news = $news->where('section_name', $request->section_name);

        if ($request->source)
            $news = $news->where('source', $request->source);

        if ($request->q) {
            $columns = $request->search_in ?: [News::$searchWithQueryString[0]];
            $queryString = $request->q;

            $news = $news->where(function ($query) use ($columns, $queryString) {

                foreach ($columns as $name)
                    $query = $query->orWhere($name, 'LIKE', "%$queryString%");

                return $query;
            });
        }

        $news = $news->paginate($request->per_page ?: 10);

        if ($request->include_body && $request->include_body != 'false' && ($request->include_body == 'true' || $request->include_body))
            $news->getCollection()
                ->each
                ->makeVisible('body');


        return response()->json([
            "news" => $news
        ]);
    }
}
