import TSRApiRESTDataSource from '../TSRApiRESTDataSource';

export default class LeadAPI extends TSRApiRESTDataSource {
    constructor() {
        // Always call super()
        super();
        // Sets the base URL for the REST API
        this.baseURL = process.env.LEAD_API_DOMAIN;
        this.resourceName = 'lead';
    }

    async createLead(
        lead_type_code: string,
        learning_provider_id: number,
        learning_provider_source: string,
        enquiry: string,
        website_source: string,
        consent_text: string,
        route?: string,
        url_path?: string,
        course_id?: number,
        subject_id?: number,
        user?: any,
        guest_user?: any,
        open_day?: string,
        mobile?: string,
        post_code?: string,
        is_postgraduate?: boolean,
        is_international?: boolean,
        country?: string,
        city?: string
    ): Promise<any> {
        const url = `lead`;
        return this.post(url, {
            data: {
                lead_type_code,
                learning_provider_id,
                learning_provider_source,
                enquiry,
                website_source,
                consent_text,
                route,
                url_path,
                course_id,
                subject_id,
                user,
                guest_user,
                open_day,
                mobile,
                post_code,
                is_postgraduate,
                is_international,
                country,
                city
            }
        })
            .then(response => {
                return {valid: true, error: null};
            })
            .catch(err => {
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }
}
