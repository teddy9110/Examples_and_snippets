<?php

namespace Rhf\Modules\Notifications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopicsRequest extends FormRequest
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
        return [
            'title' => 'required|string',
            'description' => 'string',
            'topic_id' => 'exists:topics,id',
        ];
    }
}
