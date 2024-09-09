import { LeadUserData, LeadResults } from './types';
import { ValidityCheck } from '../types';
import createLoaders from '../dataLoaders';

export default {
    Query: {
        async lead_list(parent: any, args: any, context: any, info: any): Promise<LeadResults> {
            const leads = await context.dataSources.leadAPI.getList(args.page, args.per_page, args.order_by, args.order_by_direction, args.filters);
            return {
                leads: leads.data,
                total_results: leads.total_results
            }
        },
    },
    Mutation: {
        lead_create(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            return context.dataSources.leadAPI.createLead(
                args.lead_type_code,
                args.learning_provider_id,
                args.learning_provider_source,
                args.enquiry,
                args.website_source,
                args.consent_text,
                args.route,
                args.url_path,
                args.course_id,
                args.subject_id,
                args.user,
                args.guest_user,
                args.open_day,
                args.mobile,
                args.post_code, 
                args.is_postgraduate,
                args.is_international,
                args.country,
                args.city
            );
        },
    },
    Lead: {
        async user(parent: any, args: any, context: any, info: any): Promise<LeadUserData> {
            if (parent.guest_user) {
                return parent.guest_user;
            } else if (parent.user_id) {
                const loaders:any = createLoaders(context.dataSources);
                const user = loaders.users.load(parent.user_id);
                const profile = loaders.user_profiles.load(parent.user_id);
                return Promise.all([user, profile]).then((values) => {
                    return {
                        id: values[0].id,
                        first_name: values[1].first_name,
                        last_name: values[1].last_name,
                        email: values[0].email,
                        mobile: values[1].mobile ?? '',
                        post_code: values[1].post_code ?? '',
                        country: values[1].country ?? '',
                        city: values[1].city ?? '',
                        university_start_year: values[1].intended_university_start_year
                    };
                }).catch((err) => {
                    return {
                        id: null,
                        first_name: '',
                        last_name: '',
                        email: '',
                        mobile: '',
                        post_code: '',
                        country: '',
                        city: '',
                        university_start_year: 0
                    };
                });
            }
            return {
                id: null,
                first_name: '',
                last_name: '',
                email: '',
                country: '',
                city: '',
                university_start_year: 0
            };
        },
    }
};
