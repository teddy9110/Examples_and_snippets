import resolvers from './resolvers';
import UserAPI, { UserProfileAPI, UserTypeAPI } from './data';
import MarketingAPI from '../marketing/data';
import { NotLoggedIn, NoClientId } from '../errors';
import * as createLoadersModule from "../dataLoaders";
import { KeyValue, CodeFrequency } from '../types';
import * as tokens from '../tokens';

const mockSuccess = jest.fn(() => Promise.resolve({}));

function createMockResponse(response: any) {
    return jest.fn(() => Promise.resolve(response));
}

function mockUserProfileAPI(methodMock: any, methodName: string) {
    jest.spyOn(UserProfileAPI.prototype as any, methodName).mockImplementation(methodMock);
}
function mockUserAPI(methodMock: any, methodName: string) {
    jest.spyOn(UserAPI.prototype as any, methodName).mockImplementation(methodMock);
}
function mockUserTypeAPI(methodMock: any, methodName: string) {
    jest.spyOn(UserTypeAPI.prototype as any, methodName).mockImplementation(methodMock);
}
function mockMarketingAPI(methodMock: any, methodName: string) {
    jest.spyOn(MarketingAPI.prototype as any, methodName).mockImplementation(methodMock);
}


const mockLoadUser = jest.fn(() => { return { email: 'test@test.com' }; });
const mockLoadManyUserTypes = jest.fn(() => { return [{ id: 1, name: 'test' }, { id: 2, name: 'test 2' }, { id: 3, name: 'test 3' }]; });
function mockCreateLoaders() {
    jest.spyOn(createLoadersModule, "default").mockImplementation(() => ({
        users: { load: mockLoadUser },
        user_types: { loadMany: mockLoadManyUserTypes }
    } as any));
}

describe('Query user', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user resolver calls user loader', async () => {
        mockCreateLoaders();
        await resolvers.Query.user({}, { id: 1 }, {}, {});
        expect(mockLoadUser).toBeCalledWith(1);
    });
});

describe('Query user_list', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_list resolver calls getList on the UserAPI', async () => {
        const mockResponse = createMockResponse({ data: [1], total_results: 1 });
        mockUserAPI(mockResponse, 'getList');
        const response = await resolvers.Query.user_list({}, { page: 1, per_page: 20, order_by: 'date', order_by_direction: 'desc', filters: [] }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockResponse).toBeCalledWith(1, 20, 'date', 'desc', []);
        expect(response).toEqual({
            users: [1],
            total_results: 1
        });
    });
});

describe('Query user_type', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_type resolver calls getSingle on UserTypeAPI', async () => {
        mockUserTypeAPI(mockSuccess, 'getSingle');
        await resolvers.Query.user_type({}, { id: 1 }, { dataSources: { userTypeAPI: new UserTypeAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1);
    });
});

describe('Query user_type_list', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_type_list resolver calls getList on the UserTypeAPI', async () => {
        const mockResponse = createMockResponse({ data: [1], total_results: 1 });
        mockUserTypeAPI(mockResponse, 'getList');
        const response = await resolvers.Query.user_type_list({}, { page: 1, per_page: 20, order_by: 'date', order_by_direction: 'desc', filters: [] }, { dataSources: { userTypeAPI: new UserTypeAPI() } }, {});
        expect(mockResponse).toBeCalledWith(1, 20, 'date', 'desc', []);
        expect(response).toEqual({
            user_types: [1],
            total_results: 1
        });
    });
});

describe('Query user_type_many', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_type_many resolver calls user_types loader', async () => {
        mockCreateLoaders();
        await resolvers.Query.user_type_many({}, { ids: [1, 2, 3] }, {}, {});
        expect(mockLoadManyUserTypes).toBeCalledWith([1, 2, 3]);
    });
});


describe('Mutation user_update', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_update resolver calls update on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'update');
        await resolvers.Mutation.user_update({}, { id: 1, user_type_id: 5 }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, { user_type_id: 5 });
    });

    it('user_update returns error if user_type_id is falsey', async () => {
        mockUserAPI(mockSuccess, 'update');
        const response = await resolvers.Mutation.user_update({}, { id: 1, user_type_id: 0 }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).not.toBeCalled();
        expect(response).toEqual({ valid: false, error: 'NO_DATA' });
    });
});


describe('Query userProfile', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('userProfile resolver calls getProfile on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserProfileAPI(mockSuccess, 'getProfile');
        const response = await resolvers.Query.userProfile({}, {}, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1);
        expect(response).toEqual({});
    });

    it('userProfile resolver returns error when there is no UserID', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'getProfile');
        await resolvers.Query.userProfile({}, {}, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {}).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation updateProfile', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateProfile resolver calls updateProfile on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserProfileAPI(mockSuccess, 'updateProfile');
        const response = await resolvers.Mutation.updateProfile({}, { first_name: 'Test' }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, { first_name: 'Test' });
        expect(response).toEqual({});
    });

    it('updateProfile resolver returns error when there is no UserID', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'updateProfile');
        const response = await resolvers.Mutation.updateProfile({}, { first_name: 'Test' }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).not.toBeCalled();
        expect(response).toEqual({ valid: false, error: 'NOT_LOGGED_IN' });
    });
});

describe('Mutation updateSubjects', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateSubjects resolver calls updateSubjects on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserProfileAPI(mockSuccess, 'updateSubjects');
        const response = await resolvers.Mutation.updateSubjects({}, { subject_ids: [1, 2, 3], stage: 'current' }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, [1, 2, 3], 'current');
        expect(response).toEqual({});
    });

    it('updateProfile resolver returns error when there is no UserID', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'updateSubjects');
        const response = await resolvers.Mutation.updateSubjects({}, { subject_ids: [1, 2, 3], stage: 'current' }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).not.toBeCalled();
        expect(response).toEqual({ valid: false, error: 'NOT_LOGGED_IN' });
    });
});


describe('Mutation updateLearningProviders', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateLearningProviders resolver calls updateLearningProviders on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserProfileAPI(mockSuccess, 'updateLearningProviders');
        const response = await resolvers.Mutation.updateLearningProviders({}, { learning_provider_ids: [1, 2, 3], stage: 'current' }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, [1, 2, 3], 'current');
        expect(response).toEqual({});
    });

    it('updateProfile resolver returns error when there is no UserID', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'updateLearningProviders');
        const response = await resolvers.Mutation.updateLearningProviders({}, { learning_provider_ids: [1, 2, 3], stage: 'current' }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).not.toBeCalled();
        expect(response).toEqual({ valid: false, error: 'NOT_LOGGED_IN' });
    });
});

describe('Mutation updateMarketingPreferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateMarketingPreferences resolver calls updateMarketingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        const data: KeyValue[] = [{
            key: "clearing",
            value: "Weekly"
        }];

        mockUserProfileAPI(mockSuccess, 'updateMarketingPreferences');
        const response = await resolvers.Mutation.updateMarketingPreferences({}, { data: data }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, [{
            key: "clearing",
            value: "Weekly"
        }]);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        const preferences: CodeFrequency[] = [{
            code: "B2B2C_CLEARING",
            frequency: "WEEKLY"
        }];

        mockUserProfileAPI(mockSuccess, 'updateMarketingPreferences');
        await resolvers.Mutation.updateMarketingPreferences(
            {},
            { preferences: preferences },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation marketing_preferences_update', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('marketing_preferences_update resolver calls updateMarketingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        const preferences: CodeFrequency[] = [{
            code: "B2B2C_CLEARING",
            frequency: "WEEKLY"
        }];

        mockUserProfileAPI(mockSuccess, 'updateMarketingPreferences');
        const response = await resolvers.Mutation.marketing_preferences_update({}, { preferences: preferences }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, [{
            code: "B2B2C_CLEARING",
            value: "WEEKLY"
        }]);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        const preferences: CodeFrequency[] = [{
            code: "B2B2C_CLEARING",
            frequency: "WEEKLY"
        }];

        mockUserProfileAPI(mockSuccess, 'updateMarketingPreferences');
        await resolvers.Mutation.marketing_preferences_update(
            {},
            { preferences: preferences },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation topics_of_interest_update', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('topics_of_interest_update resolver calls updateTopicsOfInterest on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        mockUserProfileAPI(mockSuccess, 'updateTopicsOfInterest');
        const response = await resolvers.Mutation.topics_of_interest_update({}, { interested_topic_codes: ['a', 'b', 'c'], uninterested_topic_codes: ['d'] }, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, ['a', 'b', 'c'], ['d']);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'updateTopicsOfInterest');
        await resolvers.Mutation.topics_of_interest_update(
            {},
            { interested_topic_codes: ['a', 'b', 'c'], uninterested_topic_codes: ['d'] },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation confirmEmail', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('confirmEmail resolver calls confirmEmail on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'confirmEmail');
        await resolvers.Mutation.confirmEmail({}, { email: 'test@test.com', token: 'token' }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith('test@test.com', 'token');
    });
});

describe('Mutation sendEmailConfirmationLink', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('sendEmailConfirmationLink resolver calls sendEmailConfirmationLink on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'sendEmailConfirmationLink');
        await resolvers.Mutation.sendEmailConfirmationLink({}, { email: 'test@test.com' }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith('test@test.com');
    });
});

describe('UserProfile email', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('email resolver calls user loader and returns email', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockCreateLoaders();
        const email = await resolvers.UserProfile.email({}, {}, { res: mockResponse }, {});
        expect(mockLoadUser).toBeCalledWith(1);
        expect(email).toEqual('test@test.com');
    });

    it('email resolver calls user loader and returns email', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(0)
        };
        mockCreateLoaders();
        const email = await resolvers.UserProfile.email({}, {}, { res: mockResponse }, {});
        expect(mockLoadUser).not.toBeCalled();
        expect(email).toEqual('');
    });
});

describe('UserProfile->user_data_sharing_preferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_data_sharing_preferences resolver calls getDataSharingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserProfileAPI(mockSuccess, 'getDataSharingPreferences');
        const response = await resolvers.UserProfile.user_data_sharing_preferences(
            {},
            {},
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        );
        expect(mockSuccess).toBeCalledWith(1);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'getDataSharingPreferences');
        await resolvers.UserProfile.user_data_sharing_preferences(
            {},
            {},
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation updateUserQualification', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateUserQualification resolver calls updateUserQualification on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        const args = {
            id: 1,
            data: [
                {
                    end_year: '2023'
                }
            ]
        };

        mockUserProfileAPI(mockSuccess, 'updateUserQualification');
        const response = await resolvers.Mutation.updateUserQualification({}, args, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(args.id, args.data);
        expect(response).toEqual({});
    });
});

// START Deprecated
describe('Mutation updateQualifications', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateQualifications resolver calls updateQualifications on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        const args = {
            qualifications: {
                qualification_ids: [
                    {
                        qualification_id: 2,
                        end_year: '2025',
                    },
                ],
            },
        };

        mockUserProfileAPI(mockSuccess, 'updateQualifications');
        const response = await resolvers.Mutation.updateQualifications({}, args, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, args.qualifications.qualification_ids);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        const args = {
            qualifications: {
                qualification_ids: [
                    {
                        qualification_id: 2,
                        end_year: '2025',
                    },
                ],
            },
        };

        mockUserProfileAPI(mockSuccess, 'updateQualifications');
        await resolvers.Mutation.updateQualifications(
            {},
            args,
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});
// END Deprecated

describe('Mutation updateQualificationStage', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateQualificationStage resolver calls updateQualificationStage on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        const args = {
            qualifications: [
                {
                    qualification_id: 2,
                },
            ],
            stage: 'current'
        };
        mockUserProfileAPI(mockSuccess, 'updateQualificationStage');
        const response = await resolvers.Mutation.updateQualificationStage({}, args, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, args.qualifications, args.stage);
        expect(response).toEqual({});
    });
});

describe('Mutation updateInternationalUserData', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('updateInternationalUserData resolver calls updateInternationalUserData on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        const args = {
            international_questions: {
                country_of_nationality: null,
                is_application_started: null,
                international_agent: null
            }
        };

        mockUserProfileAPI(mockSuccess, 'updateInternationalUserData');

        const response = await resolvers.Mutation.updateInternationalUserData({}, args, { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } }, {});

        expect(mockSuccess).toBeCalledWith(1, args);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        const args = {
            international_questions: {
                country_of_nationality: null,
                is_application_started: null,
                working_with_international_agent: null
            }
        };

        mockUserProfileAPI(mockSuccess, 'getMarketingPreferences');

        await resolvers.Mutation.updateInternationalUserData(
            {},
            args,
            {
                res: mockResponse,
                dataSources: {
                    userProfileAPI: new UserProfileAPI()
                }
            },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });

        expect(mockSuccess).not.toBeCalled();
    });
});


describe('Query usernameLogin', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });
    beforeEach(() => {
        // Mock the setAccessTokenCookies function
        jest.spyOn(tokens, 'setAccessTokenCookies').mockImplementation(() => { });
    });

    const args = {
        username: 'mocked-username',
        password: 'mocked-password',
        source: {
            additional_data: '{"key": "value"}',
        },
    };

    it('It calls getAccessTokenFromUsername on the userAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        mockUserAPI(mockSuccess, 'getAccessTokenFromUsername');
        const response = await resolvers.Query.usernameLogin({}, args, { res: mockResponse, dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalled()
        expect(response).toEqual({});
        expect(tokens.setAccessTokenCookies).toBeCalled();

    });
})

describe('Query emailLogin', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });
    beforeEach(() => {
        // Mock the setAccessTokenCookies function
        jest.spyOn(tokens, 'setAccessTokenCookies').mockImplementation(() => { });
    });

    const args = {
        email: 'mocked-email',
        password: 'mocked-password',
        source: {
            additional_data: '{"key": "value"}',
        },
    };

    it('It calls getAccessTokenFromEmail on the userAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        mockUserAPI(mockSuccess, 'getAccessTokenFromEmail');
        const response = await resolvers.Query.emailLogin({}, args, { res: mockResponse, dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalled()
        expect(response).toEqual({});
        expect(tokens.setAccessTokenCookies).toBeCalled();
    });
});

describe('Query userSegments', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('userSegments resolver calls getUserSegments on the MarketingAPI', async () => {
        const mockRequest = {
            cookies: {
                '_tsr': 'abc'
            }
        };
        mockMarketingAPI(mockSuccess, 'getUserSegments');
        const response = await resolvers.Query.userSegments({}, {}, { req: mockRequest, dataSources: { marketingAPI: new MarketingAPI() } }, {});
        expect(mockSuccess).toBeCalledWith('abc');
        expect(response).toEqual({});
    });

    it('userSegments resolver returns error when there is no ClientID', async () => {
        const mockRequest = {
            cookies: {}
        };
        mockMarketingAPI(mockSuccess, 'getUserSegments');
        await resolvers.Query.userSegments({}, {}, { req: mockRequest, dataSources: { marketingAPI: new MarketingAPI() } }, {}).catch(error => {
            expect(error).toBeInstanceOf(NoClientId);
        });
        expect(mockSuccess).not.toBeCalled();
    });

});

describe('Query user_following_list', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_following_list resolver calls getFollowing on the UserAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserAPI(mockSuccess, 'getFollowing');
        const response = await resolvers.Query.user_following_list(
            {},
            { id: 1, page: 5, per_page: 10 },
            { res: mockResponse, dataSources: { userAPI: new UserAPI() } },
            {}
        );
        expect(mockSuccess).toBeCalledWith(1, 5, 10);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserAPI(mockSuccess, 'getFollowing');
        await resolvers.Query.user_following_list(
            {},
            { id: 1, page: 5, per_page: 10 },
            { res: mockResponse, dataSources: { userAPI: new UserAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation marketing_preferences_email_unsubscribe', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('marketing_preferences_email_unsubscribe resolver calls unsubscribeEmail on the UserProfileAPI', async () => {
        const args = {
            id: '1',
            marketing_preferences_code: 'B2C_GENERAL'
        };

        mockUserProfileAPI(mockSuccess, 'unsubscribeEmail');
        await resolvers.Mutation.marketing_preferences_email_unsubscribe({}, args, { dataSources: { userProfileAPI: new UserProfileAPI() } }, {});
        expect(mockSuccess).toBeCalledWith('1', 'B2C_GENERAL');
    });
});

describe('User latestPosts', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('calls getPosts on the forumAPI with the correct parameters', async () => {
        const mockGetPosts = jest.fn().mockResolvedValue([{ id: 1, title: 'Test Post' }]);
        const mockForumAPI = {
            getPosts: mockGetPosts
        };

        const parent = { id: 123 };
        const args = {};
        const context = { dataSources: { forumAPI: mockForumAPI } };
        const info = {};

        const posts = await resolvers.User.latestPosts(parent, args, context, info);

        expect(mockGetPosts).toBeCalledWith(1, 5, 'date', 'desc', [{
            key: 'user_id',
            value: parent.id,
        }]);
        expect(posts).toEqual([{ id: 1, title: 'Test Post' }]);
    });
});

describe('RecommendedUser latestPosts', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('calls getPosts on the forumAPI with the correct parameters', async () => {
        const mockGetPosts = jest.fn().mockResolvedValue([{ id: 1, title: 'Test Post' }]);
        const mockForumAPI = {
            getPosts: mockGetPosts
        };

        const parent = { id: 123 };
        const args = {};
        const context = { dataSources: { forumAPI: mockForumAPI } };
        const info = {};

        const posts = await resolvers.RecommendedUser.latestPosts(parent, args, context, info);

        expect(mockGetPosts).toBeCalledWith(1, 5, 'date', 'desc', [{
            key: 'user_id',
            value: parent.id,
        }], true);
        expect(posts).toEqual([{ id: 1, title: 'Test Post' }]);
    });
});

describe('Query isFollowing', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('isFollowing resolver calls isFollowing on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'isFollowing');
        const response = await resolvers.Query.isFollowing({}, { user_id: 1 }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1);
        expect(response).toEqual({});
    });
});

describe('Mutation followUser', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('followUser resolver calls followUser on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'followUser');
        await resolvers.Mutation.followUser({}, { user_id: 1 }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1);
    });
});

describe('Mutation unfollowUser', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('unfollowUser resolver calls followUser on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'unfollowUser');
        await resolvers.Mutation.unfollowUser({}, { user_id: 1 }, { dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1);
    });
});

describe('Query user_data_sharing_preferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_data_sharing_preferences resolver calls getDataSharingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        mockUserProfileAPI(mockSuccess, 'getDataSharingPreferences');
        const response = await resolvers.Query.user_data_sharing_preferences(
            {},
            {},
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        );
        expect(mockSuccess).toBeCalledWith(1);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };
        mockUserProfileAPI(mockSuccess, 'getDataSharingPreferences');
        await resolvers.Query.user_data_sharing_preferences(
            {},
            {},
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation user_data_sharing_preferences_update', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_data_sharing_preferences_update resolver calls updateDataSharingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        mockUserProfileAPI(mockSuccess, 'updateDataSharingPreferences');

        const response = await resolvers.Mutation.user_data_sharing_preferences_update(
            {},
            { questionCode: 'study_level' },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        );

        expect(mockSuccess).toBeCalledWith(1, 'study_level');
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        mockUserProfileAPI(mockSuccess, 'updateDataSharingPreferences');

        await resolvers.Mutation.user_data_sharing_preferences_update(
            {},
            { questionCode: 'study_level' },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });

        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Mutation user_data_sharing_preferences_delete', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('user_data_sharing_preferences_delete resolver calls getDataSharingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        mockUserProfileAPI(mockSuccess, 'deleteDataSharingPreferences');

        const response = await resolvers.Mutation.user_data_sharing_preferences_delete(
            {},
            { questionCode: 'study_level' },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        );

        expect(mockSuccess).toBeCalledWith(1, 'study_level');
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        mockUserProfileAPI(mockSuccess, 'deleteDataSharingPreferences');

        await resolvers.Mutation.user_data_sharing_preferences_delete(
            {},
            { questionCode: 'study_level' },
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });

        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Query userIgnoreList', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('userIgnoreList resolver calls getUserIgnoreList on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'getUserIgnoreList');

        const response = await resolvers.Query.userIgnoreList(
            {},
            { page: 5, per_page: 10, order_by: 'test', order_by_direction: 'desc', filters: [] },
            { dataSources: { userAPI: new UserAPI() } },
            {}
        );

        expect(mockSuccess).toBeCalledWith(5, 10, 'test', 'desc', []);
        expect(response).toEqual({});
    });
});

describe('Query userMarketingPreferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('userMarketingPreferences resolver calls getMarketingPreferences on the UserProfileAPI', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };

        mockUserProfileAPI(mockSuccess, 'getMarketingPreferences');

        const response = await resolvers.Query.userMarketingPreferences(
            {},
            {},
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        );

        expect(mockSuccess).toBeCalledWith(1);
        expect(response).toEqual({});
    });

    it('throws an error if there is no user id in the header', async () => {
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue('')
        };

        mockUserProfileAPI(mockSuccess, 'getMarketingPreferences');

        await resolvers.Query.userMarketingPreferences(
            {},
            {},
            { res: mockResponse, dataSources: { userProfileAPI: new UserProfileAPI() } },
            {}
        ).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });

        expect(mockSuccess).not.toBeCalled();
    });
});

describe('Query recommendedUsers', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('recommendedUsers resolver calls recommendedUsers on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'getRecommendedUsers');
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        const response = await resolvers.Query.recommendedUsers({}, {}, { res: mockResponse, dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1);
        expect(response).toEqual({});
    });

    it('recommendedUsers resolver fails for guests', async () => {
        mockUserAPI(mockSuccess, 'getRecommendedUsers');
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(0)
        };
        expect(mockSuccess).not.toBeCalledWith(0);
        await resolvers.Query.recommendedUsers({}, {}, { res: mockResponse, dataSources: { userAPI: new UserAPI() } }, {}).catch(error => {
            expect(error).toBeInstanceOf(NotLoggedIn);
        });
    });
});

describe('Mutation dismissFollowSuggestion', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('recommendedUsers resolver calls dismissFollowSuggestion on the UserAPI', async () => {
        mockUserAPI(mockSuccess, 'dismissFollowSuggestion');
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(1)
        };
        const response = await resolvers.Mutation.dismissFollowSuggestion({}, { user_id: 1 }, { res: mockResponse, dataSources: { userAPI: new UserAPI() } }, {});
        expect(mockSuccess).toBeCalledWith(1, 1);
        expect(response).toEqual({});
    });

    it('recommendedUsers resolver fails for guests', async () => {
        mockUserAPI(mockSuccess, 'dismissFollowSuggestion');
        const mockResponse = {
            getHeader: jest.fn().mockReturnValue(0)
        };
        expect(mockSuccess).not.toBeCalledWith(0, 1);
        const response = await resolvers.Mutation.dismissFollowSuggestion({}, { user_id: 1 }, { res: mockResponse, dataSources: { userAPI: new UserAPI() } }, {});
        expect(response).toEqual({ valid: false });
    });
});
