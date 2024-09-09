import { gql } from 'apollo-server-core';

export default gql`
    type Lead {
        id: Int!
        status: String!
        lead_type: LeadType!
        learning_provider_id: Int!
        course_id: Int
        subject_id: Int
        mobile: String
        post_code: String
        user: LeadUser!
        created_at: String!
        updated_at: String!
    }

    type LeadType {
        code: String!
        name: String!
        credits: Int!
    }

    type Leads {
        total_results: Int!
        leads: [Lead]
    }

    type LeadUser {
        id: Int
        first_name: String!
        last_name: String!
        email: String!
        mobile: String
        post_code: String
        country: String
        city: String
        university_start_year: Int!
    }

    input LeadUserData {
        first_name: String!
        last_name: String!
        email: String!
        mobile: String
        post_code: String
        country: String
        city: String
        university_start_year: Int!
    }

    input GuestUserData {
        first_name: String!
        last_name: String!
        email: String!
        mobile: String
        post_code: String
        country: String
        city: String
        university_start_year: Int!
    }

    extend type Query {
        lead_list(page: Int,per_page: Int, order_by: String, order_by_direction: String, filters: [KeyValue]): Leads
    }

    extend type Mutation {
        lead_create(
            lead_type_code: String!,
            learning_provider_id: Int!,
            learning_provider_source: String!,
            enquiry: String!,
            website_source: String!,
            consent_text: String,
            route: String,
            url_path: String,
            course_id: Int,
            subject_id: Int,
            mobile: String,
            post_code: String,
            user: LeadUserData,
            guest_user: GuestUserData,
            open_day: String,
            is_postgraduate: Boolean
            is_international: Boolean
            country: String
            city: String
        ): ValidityCheck
    }
`;
