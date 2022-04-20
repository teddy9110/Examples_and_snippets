<?php

namespace Rhf\Modules\Recipe\Models;

use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Recipe\Services\RecipeImageFileService;
use Rhf\Modules\User\Models\User;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active',
        'title',
        'serves',
        'prep_time',
        'total_time',
        'image',
        'macro_calories',
        'macro_protein',
        'macro_carbs',
        'macro_fats',
        'macro_fibre',
    ];

    /**
     * The attributes that can be directly copied over.
     *
     * @var array
     */
    protected $plainKeys = [
        'title',
        'serves',
        'prep_time',
        'total_time'
    ];

    /**
     * The macro attributes without macro_ prefix.
     *
     * @var array
     */
    protected $macroKeys = [
        'calories',
        'protein',
        'carbs',
        'fats',
        'fibre',
    ];

     /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return RecipeFactory::new();
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Relation to ingredients.
     */
    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class, 'recipe_id', 'id');
    }

    /**
     * Relation to ingredients.
     */
    public function instructions()
    {
        return $this->hasMany(RecipeInstruction::class, 'recipe_id', 'id');
    }

    /**
     * Get the plain keys.
     */
    public function getPlainKeys()
    {
        return $this->plainKeys;
    }

    /**
     * Get the macro keys.
     */
    public function getMacroKeys()
    {
        return $this->macroKeys;
    }

    /**
     * Return the image associated to the recipe.
     *
     * @return string
     */
    public function getImage()
    {
        $fileService = new RecipeImageFileService();
        return $fileService->getPublicUrl($this);
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_favourite_recipes');
    }
}
