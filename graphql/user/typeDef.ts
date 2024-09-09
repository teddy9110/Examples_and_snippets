import { gql } from 'apollo-server-core';

export default gql`

    type TopicOfInterest {
        code: String
        interested: Boolean
    }

    type UserProfile {
        email: String
        career_phase: String
        current_qualifications: [Qualification]
        future_qualifications: [Qualification]
        current_subjects: [Int]
        future_subjects: [Int]
        current_learning_providers: [Int]
        future_learning_providers: [Int]
        first_name: String
        last_name: String
        mobile: String
        post_code: String
        city: String
        country: String
        gender: String
        international_application: InternationalQualifications
        last_updated: String
        questions_answered: Int
        email_opted_out_at: String
        sms_opted_out_at: String
        email_marketing_preferences: [CodeFrequency]
        sms_marketing_preferences: [CodeFrequency]
        user_data_sharing_preferences: UserProfileDataSharingPreference
        topics_of_interest: [TopicOfInterest]

        # START Deprecated - remove these once front-end is updated
        # These four are all covered by current_qualifications and future_qualifications
        intended_university_start_year: Int
        intended_postgraduate_start_year: Int
        qualifications: [Int]
        end_year: [QualificationsEndYear]
        # We no longer populate this one from the year group question
        year_group: Int
        # These two are marketing preferences now
        clearing_opt_in: String
        student_loans: String
        # END Deprecated


    }

    type QualificationsEndYear {
        id: Int
        end_year: String
    }

    type Qualification {
        id: Int
        qualification_id: Int
        start_year: String
        end_year: String
        current: Boolean
        previous: Boolean
        future: Boolean
    }

    type InternationalQualifications {
        country_of_nationality: String
        is_application_started: Boolean
        international_agent: String
    }

    type UserGroup  {
        primary: Boolean
        display: Boolean
        id: Int
        usertitle: String
        description: String
    }

    type UserType {
        id: Int
        code: String
        name: String
        description: String
    }

    type UserTypes {
        total_results: Int!
        user_types: [UserType]
    }

    type UserForumInfo {
        avatar: String
        join_datetime: String
        last_visit_datetime: String
        last_activity_datetime: String
        online: Boolean
        n_posts: Int
        n_rep: Int
        rep_level: Int
        usergroups: [UserGroup]
        n_badges: Int
        n_followers: Int
        title: String
        following_user_ids: [Int]
    }

    type User {
        id: Int
        username: String
        user_type_id: Int
        user_type: String
        usergroup_id: Int
        forum: UserForumInfo
        is_anonymous: Boolean
        dob: String
        gender: String
        email: String
        registration_date: String
        registration_website: String
        latestPosts: [Post]
    }

    type Users {
        total_results: Int!
        users: [User]
    }

    type RecommendedUser {
        id: Int
        username: String
        usergroup_id: Int
        forum: UserForumInfo
        is_anonymous: Boolean
        dob: String
        gender: String
        email: String
        latestPosts: [Post]
        source: String
    }

    type CodeFrequency {
        code: String
        frequency: String
    }

    type marketingPreferenceDetails {
        id: Int
        code: String
        name: String
        default_marketing_frequency:String
    }

    type UserMarketingPreference {
        user_id: Int
        marketing_preference: marketingPreferenceDetails
        frequency: String
    }

    type UserProfileDataSharingPreference {
        share_study_level: Boolean
        share_subjects_studying: Boolean
        share_university_subjects: Boolean
        share_university_choices: Boolean
        share_university_start_year: Boolean
    }

    type AccessTokens {
        user_id: Int!
        access_token: String!
        refresh_token: String!
    }

    type UserSegments {
        main: [String]!
        super: [String]!
        high: [String]!
        medium: [String]!
        low: [String]!
        all: [String]!
        adverts: [String]!
    }

    type NewUser {
        id: Int
        username: String
        tokens: AccessTokens
    }

    input UserSource {
        client_id: String
        website: String!
        source_type: String!
        url_path: String!
        location: String!
        additional_data: String
    }

    enum Provider {
        google
        facebook
        apple
    }

    input Social {
        provider: Provider!
        access_token: String!
    }

    input UserProfileQualificationsUpdate {
        qualification_ids: [QualificationInput]
    }

    input QualificationInput {
        qualification_id: Int!
        end_year: String!
    }

    input CreateQualificationInput {
        qualification_id: Int!
        end_year: String
        start_year: String
    }

    input UpdateQualificationInput {
        end_year: String
        start_year: String
        current: Boolean
        previous: Boolean
        future: Boolean
    }

    input UserProfileInternationalApplicationData {
        country_of_nationality: String
        is_application_started: Boolean
        international_agent: String
    }

    input CodeFrequencyInput {
        code: String
        frequency: String
    }

    type UserIdOnly {
        id: Int!
    }

    type IgnoredUsersList {
        ignoredUsers: [Int],
        total_results: Int
    }

    extend type Query {
        user(id: Int!): User
        user_type(id: Int!): UserType
        user_following_list(
            id: Int!,
            page: Int,
            per_page: Int
        ): [User]
        user_list(page: Int,per_page: Int, order_by: String, order_by_direction: String, filters: [KeyValue]): Users
        user_type_list(page: Int,per_page: Int, order_by: String, order_by_direction: String, filters: [KeyValue]): UserTypes
        user_type_many(ids: [Int]!): [UserType]
        emailLogin(email: String!, password: String!, source: UserSource!): AccessTokens
        usernameLogin(username: String!, password: String!, source: UserSource!): AccessTokens
        socialLogin(provider: String!, access_token: String!, source: UserSource!): AccessTokens
        checkEmail(email: String!): ValidityCheck
        checkUsername(username: String!): ValidityCheck
        checkLoggedIn: Int
        userProfile: UserProfile
        userIgnoreList(page: Int,per_page: Int, order_by: String, order_by_direction: String, filters: [KeyValue]): IgnoredUsersList
        userMarketingPreferences(id: Int!): [UserMarketingPreference]
        user_data_sharing_preferences(id: Int!): UserProfileDataSharingPreference
        userSegments: UserSegments
        recommendedUsers: [RecommendedUser]
        isFollowing(user_id: Int): Boolean
    }

    extend type Mutation {
        refreshAccessToken(refresh_token: String!, user_id: Int!): AccessTokens
        registerUser(email: String, social: Social, password: String, username: String!, dob: String!, opt_in: Boolean!, source: UserSource!): NewUser
        logOut: Boolean
        updateProfile(
            first_name: String
            last_name: String
            mobile: String
            post_code: String
            city: String
            country: String
            gender: String
            year_group: Int
            intended_university_start_year: Int
            intended_postgraduate_start_year: Int
            career_phase: String
            qualifications: [Int]
            email_opted_out_at: String
            sms_opted_out_at: String
            clearing_opt_in: String
        ): ValidityCheck
        confirmEmail(email: String! token: String!): ValidityCheck
        sendEmailConfirmationLink(email: String!): ValidityCheck

        updateSubjects(subject_ids: [Int]!, stage: String!): ValidityCheck
        updateMarketingPreferences(data: [KeyValue]!): ValidityCheck
        updateUserQualification(id: Int, data: UpdateQualificationInput): ValidityCheck
        updateQualificationStage(qualifications: [CreateQualificationInput], stage: String!): ValidityCheck
        updateInternationalUserData(international_questions: UserProfileInternationalApplicationData ): ValidityCheck
        updateLearningProviders(learning_provider_ids: [Int]!, stage: String!): ValidityCheck

        marketing_preferences_update(preferences: [CodeFrequencyInput]!): ValidityCheck
        marketing_preferences_email_unsubscribe(id: String!, marketing_preferences_code: String!): ValidityCheck
        topics_of_interest_update(interested_topic_codes: [String]!, uninterested_topic_codes: [String]!): ValidityCheck
        user_update(id: Int!, user_type_id: Int): User

        user_data_sharing_preferences_update(questionCode: String!): ValidityCheck
        user_data_sharing_preferences_delete(questionCode: String!): ValidityCheck

        dismissFollowSuggestion(user_id: Int!): ValidityCheck
        followUser(user_id: Int): Boolean
        unfollowUser(user_id: Int): Boolean

        # START Deprecated in favour of updateQualificationStage
        updateQualifications(qualifications: UserProfileQualificationsUpdate ): ValidityCheck
        # END Deprecated
    }
`;
