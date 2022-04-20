<?php

namespace Rhf\Modules\Recipe\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
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
            'id' => $this->id,
            'active' => !!$this->active,
            'title' => $this->title,
            'serves' => $this->serves,
            'prep_time' => $this->prep_time,
            'total_time' => $this->total_time,
            'image_uri' => $this->getImage(),
            'macro' => [
                'calories' => $this->macro_calories,
                'protein' => $this->macro_protein,
                'carbs' => $this->macro_carbs,
                'fats' => $this->macro_fats,
                'fibre' => $this->macro_fibre,
            ],
            'ingredients' => RecipeIngredientResource::collection($this->ingredients),
            'instructions' => RecipeInstructionResource::collection($this->instructions),
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d')
        ];
    }
}
