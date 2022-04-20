<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class CategoryRequest extends Request
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

    // OPTIONAL OVERRIDE
    public function forbiddenResponse()
    {
    }

    /**
     * Get the validation rules that apply to the request.

     * @return array
     */
    public function rules()
    {
        if (isset($this->route()->parameters()['id'])) {
            $id = $this->route()->parameters()['id'];
        } else {
            $id = 0;
        }

        switch ($this->method()) {
            case 'POST':
                return [
                    // General rules
                    'title' => 'required|max:255|unique:content_category,title,' . $id,
                    'parent_id' => '',
                ];
            case 'GET':
            case 'DELETE':
            case 'PUT':
            case 'PATCH':
                return [];
            default:
                break;
        }
    }
}
