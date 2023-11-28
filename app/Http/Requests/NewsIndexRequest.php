<?php

namespace App\Http\Requests;

use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sortBy' => ['required_with:sortType', Rule::in(News::$sortables)],
            'sortType' => 'required_with:sortBy,in:asc,desc',
            'from' => 'nullable|date_format:Y-m-d\TH:i:s',
            'to' => 'nullable|date_format:Y-m-d\TH:i:s',
            'q' => 'required_with:search_in|string',
            'search_in' => 'nullable|array',
            "search_in.*" => ['required', Rule::in(News::$searchWithQueryString)],
            "per_page" => "nullable|integer|max:50",
            'include_body' => 'nullable|in:true,false,0,1'
        ];
    }
}
