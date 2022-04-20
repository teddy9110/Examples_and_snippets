<?php

namespace Rhf\Modules\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotedProductRequest extends FormRequest
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

    public function validationData()
    {
        $result = $this->request->all();

        if ($this->query('placement') != null) {
            $result['placement'] = $this->query('placement');
        }

        return $result;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'placement' => 'sometimes|exists:promoted_product_placements,slug'
        ];
    }
}
