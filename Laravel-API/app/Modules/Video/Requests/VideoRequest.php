<?php

namespace Rhf\Modules\Video\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Rhf\Modules\Tags\Models\Tag;

class VideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $tagsString = '';
        if (isset($this->tags)) {
            $tags = Tag::where('type', 'video')->get()->pluck('slug')->toArray();
            $tagsString = implode(',', $tags);
        }

        $daily = last(explode('/', $this->getUri())) === 'daily' ?
            'required|date_format:Y-m-d' :
            'sometimes|date_format:Y-m-d';

        return [
            'page' => 'sometimes|integer',
            'limit' => 'sometimes|integer',
            'sort_by' => 'sometimes|in:recent,watched',
            'sort_direction' => 'sometimes|in:asc,desc',
            'include' => 'sometimes|string',
            'tags' => 'sometimes|array|in:' . $tagsString,
            'date' => $daily,
        ];
    }
}
