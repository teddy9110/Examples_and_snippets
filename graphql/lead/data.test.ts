import LeadAPI from './data';
import { InvalidResponseFormat } from '../errors';

const mockGetManyLeads = jest.fn(() => Promise.resolve({ data: "Leads", meta: {total: 100} }));
const mockPostLead = jest.fn(() => Promise.resolve());
const mockFailedCreateLead = jest.fn(() => Promise.reject({ extensions: { response: { body: { errors: [{ code: 'DUMMY_ERROR_CODE' }] } } } }));
const mockGetInvalidResponse = jest.fn(() => Promise.resolve(null));

function mockLeadAPI(getMock, method) {
    jest.spyOn(LeadAPI.prototype as any, method).mockImplementation(getMock);
}

describe('LeadAPI.createLead', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockLeadAPI(mockPostLead, 'post');

        const leadAPI = new LeadAPI()  
        await leadAPI.createLead(
            'DUMMY_LEAD_TYPE',
            1,
            'TUG',
            'Enquiry',
            'TUG',
            'Dummy consent text',
            'header',
            '/home',
            1,
            1,
            'DUMMY_USER',
            'DUMMY_GUEST_USER',
            'DUMMY_OPEN_DAY',
            '01234567890',
            'BN3 6AH',
            false,
            false,
            'United Kingdom',
            'london'
        );
  
        expect(mockPostLead).toBeCalledWith('lead', {
            data: {
                lead_type_code: 'DUMMY_LEAD_TYPE',
                learning_provider_id: 1,
                learning_provider_source: 'TUG',
                enquiry: 'Enquiry',
                website_source: 'TUG',
                consent_text: 'Dummy consent text',
                route: 'header',
                url_path: '/home',
                course_id: 1,
                subject_id: 1,
                user: 'DUMMY_USER',
                guest_user: 'DUMMY_GUEST_USER',
                open_day: 'DUMMY_OPEN_DAY',
                mobile: '01234567890',
                post_code: 'BN3 6AH',
                is_postgraduate: false,
                is_international: false,
                country: 'United Kingdom',
                city: 'london'
            }
        });
    });

    it('returns valid', async () => {
        mockLeadAPI(mockPostLead, 'post');

        const leadAPI = new LeadAPI()  
        const lead = await leadAPI.createLead(
            'DUMMY_LEAD_TYPE',
            1,
            'TUG',
            'Enquiry',
            'TUG',
            'Dummy consent text',
            'header',
            '/home',
            1,
            1,
            'DUMMY_USER', 
            'DUMMY_GUEST_USER',
            'DUMMY_OPEN_DAY',
            '01234567890',
            'BN3 6AH',
            false,
            false,
            'United Kingdom',
            'london'
        );
  
        expect(lead).toEqual({ valid: true, error: null});
    });

    it('returns valid if mobile and post_code arent passed', async () => {
        mockLeadAPI(mockPostLead, 'post');

        const leadAPI = new LeadAPI()  
        const lead = await leadAPI.createLead('DUMMY_LEAD_TYPE', 1, 'TUG', 'Enquiry', 'TUG', 'Dummy consent text', 'header', '/home', 1, 1, 'DUMMY_USER', 'DUMMY_GUEST_USER', 'DUMMY_OPEN_DAY');
  
        expect(lead).toEqual({ valid: true, error: null});
    });

    it('returns invalid if error on api', async () => {
        mockLeadAPI(mockFailedCreateLead, 'post');

        const leadAPI = new LeadAPI();
        const lead = await leadAPI.createLead('DUMMY_LEAD_TYPE', 1, 'TUG', 'Enquiry', 'TUG', 'Dummy consent text', 'header', '/home', 1, 1, 'DUMMY_USER', 'DUMMY_GUEST_USER', 'DUMMY_OPEN_DAY', '01234567890', 'BN3 6AH');
        expect(lead).toEqual({ valid: false, error: 'DUMMY_ERROR_CODE' });
    });
});


describe('LeadAPI.getList', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockLeadAPI(mockGetManyLeads, 'get');

        const leadAPI = new LeadAPI()  
        await leadAPI.getList(1, 20, 'created_at', 'desc', [{ key: 'learning_provider_id', value: '1'}]);
  
        expect(mockGetManyLeads).toBeCalledWith(
            'lead?page=1&per_page=20&order_by=created_at&order_by_direction=desc&learning_provider_id=1',
            null,
            { timeout: 0 }
        );
    });

    it('returns leads and total', async () => {
        mockLeadAPI(mockGetManyLeads, 'get');

        const leadAPI = new LeadAPI()  
        const leads = await leadAPI.getList(1, 20, 'created_at', 'desc', [{ key: 'learning_provider_id', value: '1'}]);
        expect(leads).toEqual({"data": "Leads", "total_results": 100});
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockLeadAPI(mockGetInvalidResponse, 'get');

        const leadAPI = new LeadAPI();
        await leadAPI.getList(1, 20, 'created_at', 'desc', [{ key: 'learning_provider_id', value: '1'}]).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});
