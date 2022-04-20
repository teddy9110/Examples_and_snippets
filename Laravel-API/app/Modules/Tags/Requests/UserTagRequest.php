<?php

namespace Rhf\Modules\Tags\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserTagRequest extends FormRequest
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
            'tags' => 'required|array|min:1',
            Rule::exists('tags')->where(function ($query) {
                $query->whereIn('id', $this->tags)->where('type', 'user');
            }),
        ];
    }
}
