import { User } from '../user/types';

interface Lead {
    id: number
    user: User
};

interface LeadResults {
    total_results: number
    leads: Lead[]
}

interface LeadUserData {
    id?: number
    first_name: string
    last_name: string
    email: string
    mobile?: string
    post_code?: string
    country?: string
    city?: string
    university_start_year: number
}

export {
    Lead,
    LeadResults,
    LeadUserData
}
