<?php

namespace Rhf\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class UserProgressRequest extends Request
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

     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'items' => 'required|array|min:2|max:2',
                    'items.*.file' => [
                        'required',
                        'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                        'dimensions:max_width=2048,max_height=2048',
                    ],
                    'items.*.type' => 'required|in:front,side',
                    'date' => 'nullable',
                    'weight' => 'nullable',
                ];
            case 'PATCH':
                return [
                    'date' => 'required',
                    'weight' => 'nullable'
                ];
            case 'GET':
            case 'DELETE':
            case 'PUT':
                return [];
            default:
                break;
        }
    }
}
