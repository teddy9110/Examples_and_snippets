<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAppStoreFeedbackRequest extends FormRequest
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
            'score' => ['required', 'integer','min:0', 'max:10'],
            'feedback_topics' => ['required', 'array'],
            'feedback_topics.*' => [Rule::exists('app_review_topics', 'slug')],
            'comments' => ['nullable','sometimes', 'string'],
        ];
    }
}
