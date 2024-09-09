<?php

namespace App\Models;

use Log;
use App\Exceptions\ProfileNotFoundException;
use App\Support\TSRGJWT;
use App\Models\UserProfileLearningProvider;
use App\Models\UserProfileQualification;
use App\Models\UserProfileSubject;
use App\Models\UserProfileTopicsOfInterest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Brick\Postcode\PostcodeFormatter;
use Brick\Postcode\InvalidPostcodeException;

class UserProfile extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profile';

    /**
     * The primary key field name
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'mobile',
        'post_code',
        'city',
        'country',
        'gender',
        'year_group',
        'intended_university_start_year',
        'intended_postgraduate_start_year',
        'career_phase',
        'email_opted_out_at',
        'sms_opted_out_at',
        'updated_at',
        'created_at',
    ];


    // Define the accessor method for the custom attribute
    public function getQuestionsAnsweredAttribute()
    {
        $questionsComplete = 0;

        foreach($this->attributes as $question => $value){
            if(($question != 'user_id' && $question != 'updated_at' && $question != 'created_at') && $value != null){
                $questionsComplete++;
            }
        }

        foreach($this->relations as $value){
            if(isset($value) && is_array($value) && count($value) > 0) {
                $questionsComplete++;
                continue;
            }
        }
        return $questionsComplete;
    }

    public function transformAudit(array $data): array
    {
        if (isset($data) && app()->bound(TSRGJWT::class)) {
            $data['user_id'] = app(TSRGJWT::class)->userId;
            $data['user_type'] =  app(TSRGJWT::class)->userGroupId;
        }
        return $data;
    }

    public function learning_providers()
    {
        return $this->hasMany(UserProfileLearningProvider::class, 'user_id');
    }

    public function qualifications()
    {
        return $this->hasMany(UserProfileQualification::class, 'user_id');
    }

    public function subjects()
    {
        return $this->hasMany(UserProfileSubject::class, 'user_id');
    }

    public function topicsOfInterest()
    {
        return $this->hasMany(UserProfileTopicsOfInterest::class, 'user_id');
    }

    public function internationalApplication()
    {
        return $this->hasOne(UserProfileInternationalApplication::class, 'user_id');
    }

    public function marketing_preferences()
    {
        return $this->hasMany(UserMarketingPreference::class, 'user_id');
    }

    public function user_data_sharing_preferences()
    {
        return $this->hasMany(UserProfileDataSharingPreference::class, 'user_id');
    }

    public function getEmailMarketingPreferences(): array
    {
        $marketingPreferences = MarketingPreference::select(
            DB::raw('marketing_preferences.code as code'),
            DB::raw('coalesce(user_marketing_preferences.frequency, marketing_preferences.default_marketing_frequency) as frequency'),
        )
            ->leftJoin('user_marketing_preferences', function (JoinClause $join) {
                $join->on('marketing_preferences.id', '=', 'user_marketing_preferences.marketing_preference_id')
                ->where('user_id', $this->user_id);
            })
            ->where('marketing_type', 'EMAIL')
            ->get();

        return $marketingPreferences->map(function ($marketingPreference) {
            return [
                'code' => $marketingPreference->code,
                'frequency' => $marketingPreference->frequency,
            ];
        })->toArray();
    }
}
