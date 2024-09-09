import resolvers from './resolvers';
import LeadAPI from './data';
import * as createLoadersModule from "../dataLoaders";

function getFunctionMock(response: any) {
    return jest.fn(() => Promise.resolve(response));
}

function mockLeadAPI(methodMock: any, methodName: string) {
    jest.spyOn(LeadAPI.prototype as any, methodName).mockImplementation(methodMock);
}

const mockLoad = jest.fn(() => null);
const mockLoadUser = jest.fn(() => { return { id: 1, email: 'test@test.com'} });
const mockLoadUserProfile = jest.fn(() => { 
    return { 
        first_name: 'Testy', 
        last_name: 'McTest',
        mobile: '123456789',
        post_code: '123456',
        country: 'United Kingdom',
        city: 'Brighton',
        intended_university_start_year: 2023
    };
});
function mockCreateLoaders(userMethodMock: any, userProfileMethodMock: any) {
    jest.spyOn(createLoadersModule, "default").mockImplementation(() => ({
        users: {load: userMethodMock},
        user_profiles: {load: userProfileMethodMock},
    } as any));
}

describe('lead_list resolver', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('calls getList on the LeadAPI', async () => {
        const mockgetList = getFunctionMock({ data: [], meta: { total: 100 } });
        mockLeadAPI(mockgetList, 'getList');
        await resolvers.Query.lead_list(
            {}, 
            { page: 2, per_page: 20, order_by: 'created_at', order_by_direction: 'desc', filters: [{ key: 'learning_provider_id', value: '1' }] }, 
            {dataSources: {leadAPI : new LeadAPI()}}, 
            {}
        );  
        expect(mockgetList).toBeCalledWith(2, 20, 'created_at', 'desc', [{ key: 'learning_provider_id', value: '1' }]);
    });
});

describe('Lead->user resolver', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('calls dataLoader when we have a user_id', async () => {
        mockCreateLoaders(mockLoadUser, mockLoadUserProfile);
        await resolvers.Lead.user({ user_id: 1}, {}, { loaders: {users: {load: mockLoadUser}}}, {});
        expect(mockLoadUser).toBeCalledWith(1);
        expect(mockLoadUserProfile).toBeCalledWith(1);
    });
    
    it('returns guest_user if it exists', async () => {
        const user = await resolvers.Lead.user({ guest_user: { id: 1, email: 'test@test.com' } }, {}, { loaders: {users: {load: mockLoad}}}, {});
        expect(user).toEqual({
            id: 1,
            email: 'test@test.com'
        });
    });

    it('returns empty user if no user_id or guest user', async () => {
        const user = await resolvers.Lead.user({}, {}, { loaders: {users: {load: mockLoad}}}, {});
        expect(user).toEqual({
            id: null,
            first_name: '',
            last_name: '',
            email: '',
            country: '',
            city: '',
            university_start_year: 0
        });
    });
});
