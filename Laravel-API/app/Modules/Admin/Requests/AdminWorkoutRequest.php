<?php

namespace Rhf\Modules\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Rhf\Modules\Workout\Models\Workout;

class AdminWorkoutRequest extends FormRequest
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

    public function rules()
    {
        return [
            'workout_flow' => ['required', Rule::in([Workout::FLOW_YOUTUBE, Workout::FLOW_STANDARD])],

            // All flows
            'title' => 'required|string|max:255',
            'descriptive_title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'thumbnail' => [
                'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'order' => 'required|integer',

            'exercise_frequency_id' => 'nullable|exists:exercise_frequency,id',
            'exercise_level_id' => 'nullable|exists:exercise_level,id',
            'exercise_location_id' => 'nullable|exists:exercise_location,id',

            // Currently not used
            'youtube_flow_thumbnail' => [
                'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'standard_flow_thumbnail' => [
                'nullable',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],

            // Standard flow
            'video' => 'nullable',
            'rounds.*.title' => 'required|string|max:255',
            'rounds.*.content' => 'nullable|string',
            'rounds.*.order' => 'required|integer',
            'rounds.*.roundExercises.*.exercise_id' => 'required|exists:exercise,id',
            'rounds.*.roundExercises.*.quantity' => 'nullable|string',
            'rounds.*.roundExercises.*.order' => 'required|integer',

            // Youtube flow
            'youtube' => [
                'nullable',
                'regex:/^(?:https?:\/\/(?:www.)?)?youtube.com|(youtu\.be\/)/i',
            ],
            'promoted_product_id' => 'nullable|exists:promoted_products,id',
            'duration' => "required_if:workout_flow,{Workout::FLOW}|string|max:50",
            'related_videos.*.title' => 'required|string|max:255',
            'related_videos.*.url' => [
                'required',
                'regex:/^(?:https?:\/\/(?:www.)?)?youtube.com|(youtu\.be\/)/i',
            ],
            'related_videos.*.thumbnail' => [
                $this->method() === 'post' ? 'required' : 'required_without:related_videos.*.id',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp',
            ],
        ];
    }
}
