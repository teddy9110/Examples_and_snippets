<?php

namespace Rhf\Modules\Recipe\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeInstructionResource extends JsonResource
{
    protected $meta = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'order' => $this->order,
            'type' => $this->type,
            'value' => $this->value
        ];
    }
}
