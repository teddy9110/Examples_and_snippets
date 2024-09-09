interface UserGroup {
    primary: boolean
    display: boolean
    id: number
    usertitle: string
    description: string
};

interface UserForumInfo {
    avatar: string,
    join_datetime: string,
    last_visit_datetime: string,
    last_activity_datetime: string,
    online: boolean,
    n_posts: number,
    n_rep: number,
    rep_level: number,
    usergroups: UserGroup[],
    n_badges: number,
    n_followers: number,
    title: string,
    following_user_ids: number[]
};

interface User {
    id: number
    username: string
    usergroup_id: number
    forum: UserForumInfo
    dob: string
    gender: string
    email: string
};


interface UserResults {
    total_results: number
    users: User[]
}

export interface RecommendedUser {
    id: number
    username: string
    usergroup_id: number
    forum: UserForumInfo
    dob: string
    gender: string
    email: string
};


interface AccessTokens {
    access_token: string
    refresh_token: string
}

interface NewUser {
    id: number
    username: string
    tokens: AccessTokens
}

export interface UserSource {
    client_id?: string | null,
    website: string
    source_type: string
    url_path: string
    location: string
    additional_data: any
}

interface RegisterUserParams {
    email: string,
    password?: string,
    social?: {
        provider: 'google' | 'facebook' | 'apple',
        id: string
    },
    username: string,
    dob: string,
    source: UserSource
}

interface UserProfile {
    email: string
    first_name: string
    last_name: string
    mobile: string
    post_code: string
    city: string
    country: string
    gender: string
    year_group: number
    current_qualifications: [Qualification]
    future_qualifications: [Qualification]
    current_subjects: [number]
    future_subjects: [number]
    current_learning_providers: [number]
    future_learning_providers: [number]
    career_phase: string
    international_applications: [InternationalQualifications]
    last_updated: Date
    questions_answered: number
    email_opted_out_at: String
    sms_opted_out_at: String
    email_marketing_preferences: [CodeFrequency]
    sms_marketing_preferences: [CodeFrequency]
    user_data_sharing_preferences: UserProfileDataSharingPreference
    topics_of_interest: [TopicOfInterest]

    // START Deprecated - remove these once front-end is updated
    // These four are all covered by current_qualifications and future_qualifications
    intended_university_start_year: number
    intended_postgraduate_start_year: number
    end_year: [QualificationsEndYear]
    qualifications: [number]
    // These two are marketing preferences now
    student_loans: string
    clearing_opt_in: string
    // END Deprecated
}

interface QualificationsEndYear {
    id: number
    end_year: string
}

interface Qualification {
    id: number;
    qualification_id: number;
    start_year: string | null;
    end_year: string | null;
    current: boolean;
    previous: boolean;
    future: boolean;
}

interface CreateQualification {
    qualification_id: number
    end_year?: string | null;
    start_year?: string | null;
}

interface UpdateQualification {
    end_year?: string | null;
    start_year?: string | null;
    current?: boolean;
    previous?: boolean;
    future?: boolean;
}

interface InternationalQualifications {
    country_of_nationality: string
    is_application_started: boolean
    international_agent: string
}

interface UserProfileUpdate {
    first_name?: string
    last_name?: string
    mobile?: string
    post_code?: string
    clearing_opt_in?: string
    country?: string
    gender?: string
    year_group?: number
    intended_university_start_year?: number
    career_phase?: string
    study_levels?: [number]
}

interface UserProfileInternationalUpdate {
    international_questions: {
        country_of_nationality?: string,
        is_application_started?: boolean,
        international_agent?: string
    }
}

interface UserProfileQualificationsUpdate {
    qualification_ids: {
        qualification_id: number
        end_year: string
    }
}

interface CodeFrequency {
    code: string
    frequency: string
}

interface MarketingPreference {
    id: number
    marketing_preference: {
        id: number
        code: string
        name: string
        default_marketing_frequency: string
    }
    frequency: string
}

interface UserProfileDataSharingPreference {
    share_study_level: boolean
    share_subjects_studying: boolean
    share_university_subjects: boolean
    share_university_choices: boolean
    share_university_start_year: boolean
}

interface TopicOfInterest {
    code: string
    interested: boolean
}

interface IgnoredUsersList {
    ignoredUsers: [number],
    total_results: number
}

interface UserType {
    id: number
    code: string
    name: string
    description: string
}

interface UserTypeResults {
    total_results: number
    user_types: [UserType]
}

export {
    User,
    UserResults,
    AccessTokens,
    NewUser,
    RegisterUserParams,
    UserProfile,
    UserProfileUpdate,
    Qualification,
    CreateQualification,
    UpdateQualification,
    MarketingPreference,
    UserProfileDataSharingPreference,
    UserProfileQualificationsUpdate,
    UserProfileInternationalUpdate,
    IgnoredUsersList,
    UserType,
    UserTypeResults
};
