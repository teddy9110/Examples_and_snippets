import UserAPI, { UserProfileAPI } from './data';
import { InvalidResponseFormat } from '../errors';
import { KeyValue } from '../types';

const mockGet = jest.fn(() => Promise.resolve({ data: "User" }));
const mockGetUserProfile = jest.fn(() => Promise.resolve({ data: { user_profile: "User" } }));
const mockGetIgnoreList = jest.fn(() => Promise.resolve({ data: { ignoredUsers: [123], total_results: 1 } }));

const mockGetFollowing = jest.fn(() => Promise.resolve({ data: { user_following_list: [123] } }));
const mockIsFollowing = jest.fn(() => Promise.resolve({ following: true }));
const mockFollowError = jest.fn(() => Promise.reject(false));

const mockGetDataSharingPreferences = jest.fn(() => Promise.resolve({ data: { data_sharing_preferences: { "share_study_level": true } } }));
const mockPostDataSharingPreferences = jest.fn(() => Promise.resolve({ data: { valid: true, error: null } }));
const mockDeleteDataSharingPreferences = jest.fn(() => Promise.resolve({ data: { valid: true, error: null } }));

const mockPost = jest.fn(() => Promise.resolve({ data: "User" }));
const mockPatch = jest.fn(() => Promise.resolve());
const mockPatchWithResponse = jest.fn(() => Promise.resolve({ data: "User" }));
const mockPut = jest.fn(() => Promise.resolve());
const mockDelete = jest.fn(() => Promise.resolve());

const mockInvalidResponse = jest.fn(() => Promise.resolve({}));
const mockEmptyData = jest.fn(() => Promise.resolve({ data: {} }));
const mockUnknownErrorResponse = jest.fn(() => Promise.reject({ errors: [{ code: 'UNKNOWN', message: '' }] }));
const mockErrorResponse = jest.fn(
    () => Promise.reject({
        extensions: {
            response: {
                body: {
                    errors: [{ code: 'ERROR_CODE', message: '' }]
                }
            }
        }
    })
);

function mockUserAPI(methodMock: any, methodName: string) {
    jest.spyOn(UserAPI.prototype as any, methodName).mockImplementation(methodMock);
}

function mockUserProfileAPI(methodMock: any, methodName: string) {
    jest.spyOn(UserProfileAPI.prototype as any, methodName).mockImplementation(methodMock);
}


describe('UserAPI->update', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('update requests data from correct URL', async () => {
        mockUserAPI(mockPatchWithResponse, 'patch');

        const userAPI = new UserAPI()
        await userAPI.update(1, { test: 'def' });

        expect(mockPatchWithResponse).toBeCalledWith(
            'user/1',
            { data: { test: 'def' } },
            { timeout: 0 }
        );
    });

    it('update returns data', async () => {
        mockUserAPI(mockPatchWithResponse, 'patch');

        const userAPI = new UserAPI()
        const user = await userAPI.update(1, { test: 'def' });

        expect(user).toEqual("User");
    });

    it('update throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockPatch, 'patch');

        const userAPI = new UserAPI()
        await userAPI.update(1, { test: 'def' }).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->requestUser', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI()
        await userAPI.requestUser(1);

        expect(mockGet).toBeCalledWith(
            'user/1',
            null,
            { timeout: 0 }
        );
    });

    it('returns data', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI();
        const user = await userAPI.requestUser(1);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'get');

        const userAPI = new UserAPI();
        await userAPI.requestUser(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->getAccessTokenFromEmail', () => {
    const exampleLoginSource = {
        website: 'string',
        source_type: 'string',
        url_path: 'string',
        location: 'string',
        additional_data: {}
    };

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI()
        await userAPI.getAccessTokenFromEmail('email', 'password', exampleLoginSource);

        expect(mockPost).toBeCalledWith('token', { data: { email: 'email', password: 'password', source: exampleLoginSource } });
    });

    it('returns data', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const user = await userAPI.getAccessTokenFromEmail('email', 'password', exampleLoginSource);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'post');

        const userAPI = new UserAPI();
        await userAPI.getAccessTokenFromEmail('email', 'password', exampleLoginSource).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->getAccessTokenFromUsername', () => {
    const exampleLoginSource = {
        website: 'string',
        source_type: 'string',
        url_path: 'string',
        location: 'string',
        additional_data: {}
    };

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI()
        await userAPI.getAccessTokenFromUsername('username', 'password', exampleLoginSource);

        expect(mockPost).toBeCalledWith('token', { data: { username: 'username', password: 'password', source: exampleLoginSource } });
    });

    it('returns data', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const user = await userAPI.getAccessTokenFromUsername('username', 'password', exampleLoginSource);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'post');

        const userAPI = new UserAPI();
        await userAPI.getAccessTokenFromUsername('username', 'password', exampleLoginSource).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->getAccessTokenFromSocial', () => {
    const exampleLoginSource = {
        website: 'string',
        source_type: 'string',
        url_path: 'string',
        location: 'string',
        additional_data: {}
    };

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI()
        await userAPI.getAccessTokenFromSocial('provider', 'access_token', exampleLoginSource);

        expect(mockPost).toBeCalledWith('token', { data: { social: { provider: 'provider', access_token: 'access_token' }, source: exampleLoginSource } });
    });

    it('returns data', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const user = await userAPI.getAccessTokenFromSocial('provider', 'access_token', exampleLoginSource);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'post');

        const userAPI = new UserAPI();
        await userAPI.getAccessTokenFromSocial('provider', 'access_token', exampleLoginSource).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});


describe('UserAPI->getAccessTokenFromTSRCookies', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI()
        await userAPI.getAccessTokenFromTSRCookies(1, 'password', 'session_hash');

        expect(mockPost).toBeCalledWith('legacy/autologin', { data: { user_id: 1, password: 'password', session_hash: 'session_hash' } });
    });

    it('returns data', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const user = await userAPI.getAccessTokenFromTSRCookies(1, 'password', 'session_hash');

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'post');

        const userAPI = new UserAPI();
        await userAPI.getAccessTokenFromTSRCookies(1, 'password', 'session_hash').catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->refreshAccessToken', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI()
        await userAPI.refreshAccessToken('refresh_token', 1);

        expect(mockPost).toBeCalledWith('access_token', { data: { refresh_token: 'refresh_token', user_id: 1 } }, undefined);
    });

    it('returns data', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const user = await userAPI.refreshAccessToken('refresh_token', 1);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'post');

        const userAPI = new UserAPI();
        await userAPI.refreshAccessToken('refresh_token', 1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->checkUsername', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI();
        await userAPI.checkUsername('usernametocheck');

        expect(mockGet).toBeCalledWith('user/usernamecheck/usernametocheck');
    });

    it('returns valid: true on successful response', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI();
        const valid = await userAPI.checkUsername('username');

        expect(valid).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserAPI(mockUnknownErrorResponse, 'get');

        const userAPI = new UserAPI();
        const valid = await userAPI.checkUsername('username');

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false with error on failed response', async () => {
        mockUserAPI(mockErrorResponse, 'get');

        const userAPI = new UserAPI();
        const valid = await userAPI.checkUsername('username');

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserAPI->checkEmail', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI();
        await userAPI.checkEmail('test@test.com');

        expect(mockGet).toBeCalledWith('user/emailcheck/test%40test.com');
    });

    it('returns valid: true on successful response', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI();
        const valid = await userAPI.checkEmail('test@test.com');

        expect(valid).toEqual({ valid: true });
    });

    it('returns valid: false with unknown error on failed response', async () => {
        mockUserAPI(mockUnknownErrorResponse, 'get');

        const userAPI = new UserAPI();
        const valid = await userAPI.checkEmail('test@test.com');

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserAPI(mockErrorResponse, 'get');

        const userAPI = new UserAPI();
        const valid = await userAPI.checkEmail('test@test.com');

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserAPI->registerUser', () => {
    const exampleUserRequest = {
        email: 'string',
        password: 'string',
        username: 'string',
        dob: 'string',
        source: {
            website: 'string',
            source_type: 'string',
            url_path: 'string',
            location: 'string',
            additional_data: {}
        }
    }

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI()
        await userAPI.registerUser(exampleUserRequest);

        expect(mockPost).toBeCalledWith('user', { data: exampleUserRequest });
    });

    it('returns data', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const user = await userAPI.registerUser(exampleUserRequest);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'post');

        const userAPI = new UserAPI();
        await userAPI.registerUser(exampleUserRequest).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->confirmEmail', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'put');

        const userAPI = new UserAPI();
        await userAPI.confirmEmail('test@test.com', 'token');

        expect(mockPost).toBeCalledWith('user/email/confirm', { data: { email: 'test@test.com', token: 'token' } });
    });

    it('returns valid: true on successful response', async () => {
        mockUserAPI(mockPost, 'put');

        const userAPI = new UserAPI();
        const valid = await userAPI.confirmEmail('test@test.com', 'token');

        expect(valid).toEqual({ valid: true });
    });

    it('returns valid: false with unknown error on failed response', async () => {
        mockUserAPI(mockUnknownErrorResponse, 'put');

        const userAPI = new UserAPI();
        const valid = await userAPI.confirmEmail('test@test.com', 'token');

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserAPI(mockErrorResponse, 'put');

        const userAPI = new UserAPI();
        const valid = await userAPI.confirmEmail('test@test.com', 'token');

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserAPI->sendEmailConfirmationLink', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        await userAPI.sendEmailConfirmationLink('test@test.com');

        expect(mockPost).toBeCalledWith('user/email/confirm', { data: { email: 'test@test.com' } });
    });

    it('returns valid: true on successful response', async () => {
        mockUserAPI(mockPost, 'post');

        const userAPI = new UserAPI();
        const valid = await userAPI.sendEmailConfirmationLink('test@test.com');

        expect(valid).toEqual({ valid: true });
    });

    it('returns valid: false with unknown error on failed response', async () => {
        mockUserAPI(mockUnknownErrorResponse, 'post');

        const userAPI = new UserAPI();
        const valid = await userAPI.sendEmailConfirmationLink('test@test.com');

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserAPI(mockErrorResponse, 'post');

        const userAPI = new UserAPI();
        const valid = await userAPI.sendEmailConfirmationLink('test@test.com');

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserProfileAPI->getProfile', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockGetUserProfile, 'get');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.getProfile(1);

        expect(mockGetUserProfile).toBeCalledWith('profile/1');
    });

    it('returns api response on successful response', async () => {
        mockUserProfileAPI(mockGetUserProfile, 'get');

        const userProfileAPI = new UserProfileAPI();
        const profile = await userProfileAPI.getProfile(1);

        expect(profile).toEqual("User");
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'get');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.getProfile(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserProfileAPI->updateProfile', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateProfile(1, {});

        expect(mockPatch).toBeCalledWith('profile/1', { data: {} });
    });

    it('returns valid:true on successful response', async () => {
        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateProfile(1, {});

        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'patch');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateProfile(1, {}).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserProfileAPI->updateSubjects', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateSubjects(1, [1, 2, 3], 'current');

        expect(mockPut).toBeCalledWith('profile/1/subject/current', { data: { subject_ids: [1, 2, 3] } });
    });

    it('returns valid:true on successful response', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateSubjects(1, [], 'current');

        expect(response).toEqual({ valid: true });
    });

    it('checks for error response', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateSubjects(1, [], 'current').catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateSubjects(1, [], 'current').catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserProfileAPI->updateLearningProviders', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateLearningProviders(1, [1, 2, 3], 'current');

        expect(mockPut).toBeCalledWith('profile/1/learning_provider/current', { data: { learning_provider_ids: [1, 2, 3] } });
    });

    it('returns valid:true on successful response', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateLearningProviders(1, [], 'current');

        expect(response).toEqual({ valid: true });
    });
    
    it('returns valid:false with specific error code on error response', async () => {
        const error = {
            extensions: {
                response: {
                    body: {
                        errors: [
                            { code: 'ERROR_CODE' }
                        ]
                    }
                }
            }
        };
        mockPut.mockRejectedValueOnce(error);

        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateLearningProviders(1, [1, 2, 3], 'current');

        expect(response).toEqual({ valid: false, error: 'ERROR_CODE' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateLearningProviders(1, [], 'current').catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

});

describe('UserProfileAPI->updateMarketingPreferences', () => {
    const data: KeyValue[] = [{
        key: "clearing",
        value: "Weekly"
    }];

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateMarketingPreferences(1, data);

        expect(mockPut).toBeCalledWith('profile/1/marketing_preferences', { data });
    });

    it('returns valid:true on successful response', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const data: KeyValue[] = [{
            key: "clearing",
            value: "Weekly"
        }];

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateMarketingPreferences(1, data);

        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'put');

        const data: KeyValue[] = [{
            key: "clearing",
            value: "Weekly"
        }];

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateMarketingPreferences(1, data).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('returns error:UNKNOWN on failed response', async () => {
        const data: KeyValue[] = [{
            key: "clearing",
            value: "Weekly"
        }];

        mockUserProfileAPI(mockUnknownErrorResponse, 'put');
        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateMarketingPreferences(1, data)

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns error:ERROR_CODE on failed response', async () => {
        const data: KeyValue[] = [{
            key: "clearing",
            value: "Weekly"
        }];

        mockUserProfileAPI(mockErrorResponse, 'put');
        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateMarketingPreferences(1, data)

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserProfileAPI->updateTopicsOfInterest', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateTopicsOfInterest(1, ['a', 'b', 'c'], ['d']);

        expect(mockPut).toBeCalledWith('profile/1/topics', { data: { interested_topic_codes: ['a', 'b', 'c'], uninterested_topic_codes: ['d'] } });
    });

    it('returns valid:true on successful response', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateTopicsOfInterest(1, ['a', 'b', 'c'], ['d']);

        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateTopicsOfInterest(1, ['a', 'b', 'c'], ['d']).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('returns error:UNKNOWN on failed response', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'put');
        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateTopicsOfInterest(1, ['a', 'b', 'c'], ['d'])

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns error:ERROR_CODE on failed response', async () => {
        mockUserProfileAPI(mockErrorResponse, 'put');
        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateTopicsOfInterest(1, ['a', 'b', 'c'], ['d'])

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserProfileAPI->getMarketingPreferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockGet, 'get');

        const userProfileAPI = new UserProfileAPI()
        await userProfileAPI.getMarketingPreferences(1);

        expect(mockGet).toBeCalledWith('profile/1/marketing_preferences');
    });

    it('returns data', async () => {
        mockUserProfileAPI(mockGet, 'get');

        const userProfileAPI = new UserProfileAPI();
        const user = await userProfileAPI.getMarketingPreferences(1);

        expect(user).toEqual('User');
    });
});

describe('UserProfileAPI->updateUserQualification', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('returns valid:true on successful response', async () => {
        const qualificationsData = {
            end_year: '2024'
        };

        mockUserProfileAPI(mockPatch, 'patch');
        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateUserQualification(1, qualificationsData);
        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response and unknown error', async () => {
        const qualificationsData = {
            end_year: '2024'
        };
        mockUserProfileAPI(mockUnknownErrorResponse, 'patch');
        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateUserQualification(1, qualificationsData);
        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });
});

// START Deprecated
describe('UserProfileAPI->updateQualifications', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {

        const qualificationsData = [
            {
                qualification_ids: {
                    qualification_id: 1,
                    end_year: '2023',
                },
            },
        ];

        const expectedObject = {
            data: {
                qualification_ids: [
                    {
                        qualification_ids: {
                            end_year: "2023",
                            qualification_id: 1,
                        },
                    },
                ]
            }
        };

        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateQualifications(1, qualificationsData);
        expect(mockPatch).toBeCalledWith('profile/1/qualifications', expectedObject);
    });

    it('returns valid:true on successful response', async () => {
        const qualificationsData = [
            {
                qualification_ids: {
                    qualification_id: 1,
                    end_year: '2023',
                },
            },
        ];

        mockUserProfileAPI(mockPatch, 'patch');
        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateQualifications(1, qualificationsData);
        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'put');

        const qualificationsData = [
            {
                qualification_ids: {
                    qualification_id: 1,
                    end_year: '2023',
                },
            },
        ];

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateQualifications(1, qualificationsData).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('returns valid: false on failed response and unknown error', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'patch');

        const qualificationsData = [
            {
                qualification_ids: {
                    qualification_id: 1,
                    end_year: '2023',
                },
            },
        ];

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateQualifications(1, qualificationsData);
        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockErrorResponse, 'patch');

        const qualificationsData = [
            {
                qualification_ids: {
                    qualification_id: 1,
                    end_year: '2023',
                },
            },
        ];

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateQualifications(1, qualificationsData);
        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});
// END Deprecated

describe('UserProfileAPI->updateQualificationStage', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        const qualificationsData = [
            {
                qualification_id: 1
            },
            {
                qualification_id: 2
            }
        ];

        const expectedObject = {
            data: {
                qualifications: [
                    {
                        qualification_id: 1
                    },
                    {
                        qualification_id: 2
                    }
                ]
            }
        };

        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateQualificationStage(1, qualificationsData, 'current');
        expect(mockPut).toBeCalledWith('profile/1/qualifications/current', expectedObject);
    });

    it('returns valid:true on successful response', async () => {
        const qualificationsData = [
            {
                qualification_id: 1
            },
            {
                qualification_id: 2
            }
        ];

        mockUserProfileAPI(mockPut, 'put');
        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateQualificationStage(1, qualificationsData, 'current');
        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response and unknown error', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'put');
        const qualificationsData = [
            {
                qualification_id: 1
            },
            {
                qualification_id: 2
            }
        ];

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateQualificationStage(1, qualificationsData, 'current');
        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });
});

describe('UserProfileAPI->unsubscribeEmail', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.unsubscribeEmail('1', 'B2C_GENERAL');

        expect(mockPut).toBeCalledWith('profile/marketing_preferences/unsubscribe', { data: { user_id: '1', marketing_preferences_code: 'B2C_GENERAL' } });
    });

    it('returns valid:true on successful response', async () => {
        mockUserProfileAPI(mockPut, 'put');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.unsubscribeEmail('1', 'B2C_GENERAL');

        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.unsubscribeEmail('1', 'B2C_GENERAL').catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('returns valid: false on failed response and unknown error', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.unsubscribeEmail('1', 'B2C_GENERAL');

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockErrorResponse, 'put');

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.unsubscribeEmail('1', 'B2C_GENERAL');

        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});


describe('UserProfileAPI->updateInternationalUserData', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        const internationalData = {
            international_questions: {
                country_of_nationality: 'country 2',
                is_application_started: false,
                international_agent: 'test'
            }
        };

        const expectedObject = {
            data: {
                international_questions: {
                    country_of_nationality: 'country 2',
                    is_application_started: false,
                    international_agent: 'test'
                }
            }
        };

        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateInternationalUserData(1, internationalData);

        expect(mockPatch).toBeCalledWith('profile/1/international', expectedObject);
    });

    it('returns valid:true on successful response', async () => {
        const internationalData = {
            international_questions: {
                country_of_nationality: 'country 2',
                is_application_started: false,
                international_agent: 'test'
            }
        };

        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateInternationalUserData(1, internationalData);

        expect(response).toEqual({ valid: true });
    });

    it('returns valid:true on successful response with missing country data', async () => {
        const internationalData = {
            international_questions: {
                is_application_started: false,
                international_agent: 'test'
            }
        };

        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateInternationalUserData(1, internationalData);

        expect(response).toEqual({ valid: true });
    });

    it('returns valid:true on successful response with missing application', async () => {
        const internationalData = {
            international_questions: {
                country_of_nationality: 'country 2',
            }
        };

        mockUserProfileAPI(mockPatch, 'patch');

        const userProfileAPI = new UserProfileAPI();
        const response = await userProfileAPI.updateInternationalUserData(1, internationalData);

        expect(response).toEqual({ valid: true });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'patch');

        const internationalData = {
            international_questions: {
                country_of_nationality: 'country 2',
                is_application_started: false,
                international_agent: 'test'
            }
        };

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateInternationalUserData(1, internationalData).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('returns valid: false on failed response and unknown error', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'patch');

        const internationalData = {
            international_questions: {
                country_of_nationality: 'country 2',
                is_application_started: false,
                international_agent: 'test'
            }
        };

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateInternationalUserData(1, internationalData);

        expect(valid).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false on failed response', async () => {
        mockUserProfileAPI(mockErrorResponse, 'patch');

        const internationalData = {
            international_questions: {
                country_of_nationality: 'country 2',
                is_application_started: false,
                working_with_international_agent: true
            }
        };

        const userProfileAPI = new UserProfileAPI();
        const valid = await userProfileAPI.updateInternationalUserData(1, internationalData);
        expect(valid).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserAPI->getUserIgnoreList', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockGetIgnoreList, 'get');

        const userAPI = new UserAPI()
        await userAPI.getUserIgnoreList();

        expect(mockGetIgnoreList).toBeCalledWith(
            'user/ignore/list?page=1&per_page=5&order_by=date&order_by_direction=desc',
            null,
            { timeout: 0 }
        );
    });

    it('requests data with filters', async () => {
        mockUserAPI(mockGetIgnoreList, 'get');

        const userAPI = new UserAPI()
        await userAPI.getUserIgnoreList(1, 5, 'date', 'desc', [{ key: 'filter-param', value: 'test' }]);

        expect(mockGetIgnoreList).toBeCalledWith(
            'user/ignore/list?page=1&per_page=5&order_by=date&order_by_direction=desc&filter-param=test',
            null,
            { timeout: 0 }
        );
    });

    it('returns data', async () => {
        mockUserAPI(mockGetIgnoreList, 'get');

        const userAPI = new UserAPI();
        const userIgnoreList = await userAPI.getUserIgnoreList();

        expect(userIgnoreList).toEqual({ "ignoredUsers": [123], "total_results": 1 });
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'get');

        const userAPI = new UserAPI();
        await userAPI.getUserIgnoreList(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->getFollowing', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockGetFollowing, 'get');

        const userAPI = new UserAPI()
        await userAPI.getFollowing(1);

        expect(mockGetFollowing).toBeCalledWith(
            'user/1/following?page=1&per_page=5',
            null,
            { timeout: 0 }
        );
    });

    it('requests data from correct URL with query params', async () => {
        mockUserAPI(mockGetFollowing, 'get');

        const userAPI = new UserAPI()
        await userAPI.getFollowing(1, 5, 10);

        expect(mockGetFollowing).toBeCalledWith(
            'user/1/following?page=5&per_page=10',
            null,
            { timeout: 0 }
        );
    });

    it('returns data', async () => {
        mockUserAPI(mockGetFollowing, 'get');

        const userAPI = new UserAPI();
        const following = await userAPI.getFollowing(1);

        expect(following).toEqual({ "user_following_list": [123] });
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'get');

        const userAPI = new UserAPI();
        await userAPI.getFollowing(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->getRecommendedUsers', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI()
        await userAPI.getRecommendedUsers(1);

        expect(mockGet).toBeCalledWith('user/1/recommend');
    });

    it('returns data', async () => {
        mockUserAPI(mockGet, 'get');

        const userAPI = new UserAPI();
        const user = await userAPI.getRecommendedUsers(1);

        expect(user).toEqual('User');
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserAPI(mockInvalidResponse, 'get');

        const userAPI = new UserAPI();
        await userAPI.getRecommendedUsers(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserAPI->isFollowing', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });
  
    it('requests data from correct URL', async () => {
        mockUserAPI(mockIsFollowing, 'get');

        const userAPI = new UserAPI();
        await userAPI.isFollowing(1);

        expect(mockIsFollowing).toBeCalledWith('user/following/1');
    });

    it('returns true on successful response', async () => {
        mockUserAPI(mockIsFollowing, 'get');

        const userAPI = new UserAPI();
        const following = await userAPI.isFollowing(1);

        expect(following).toEqual(true);
    });

    it('returns false on error response', async () => {
        mockUserAPI(mockFollowError, 'get');

        const userAPI = new UserAPI();
        const following = await userAPI.isFollowing(1);

        expect(following).toEqual(false);
    });
});

describe('UserAPI->followUser', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });
  
    it('requests data from correct URL', async () => {
        mockUserAPI(mockPut, 'put');

        const userAPI = new UserAPI();
        await userAPI.followUser(1);

        expect(mockPut).toBeCalledWith('user/follow/1');
    });

    it('returns true on successful response', async () => {
        mockUserAPI(mockPut, 'put');

        const userAPI = new UserAPI();
        const following = await userAPI.followUser(1);

        expect(following).toEqual(true);
    });

    it('returns false on error response', async () => {
        mockUserAPI(mockFollowError, 'put');

        const userAPI = new UserAPI();
        const following = await userAPI.followUser(1);

        expect(following).toEqual(false);
    });
});

describe('UserAPI->unfollowUser', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserAPI(mockDelete, 'delete');

        const userAPI = new UserAPI();
        await userAPI.unfollowUser(1);

        expect(mockDelete).toBeCalledWith('user/follow/1');
    });

    it('returns true on successful response', async () => {
        mockUserAPI(mockDelete, 'delete');

        const userAPI = new UserAPI();
        const unfollowed = await userAPI.unfollowUser(1);

        expect(unfollowed).toEqual(true);
    });

    it('returns false on error response', async () => {
        mockUserAPI(mockFollowError, 'delete');

        const userAPI = new UserAPI();
        const unfollowed = await userAPI.unfollowUser(1);

        expect(unfollowed).toEqual(false);
    });
});

describe('UserProfileAPI->getDataSharingPreferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockGetDataSharingPreferences, 'get');

        const userProfileAPI = new UserProfileAPI()
        await userProfileAPI.getDataSharingPreferences(1);

        expect(mockGetDataSharingPreferences).toBeCalledWith('profile/1/data_sharing_preferences');
    });

    it('returns data', async () => {
        mockUserProfileAPI(mockGetDataSharingPreferences, 'get');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingPreferences = await userProfileAPI.getDataSharingPreferences(1);

        expect(dataSharingPreferences.share_study_level).toEqual(true);
    });

    it('throws InvalidResponseFormat if data isnt in response object', async () => {
        mockUserProfileAPI(mockInvalidResponse, 'get');

        const userProfileAPI = new UserProfileAPI();
        await  userProfileAPI.getDataSharingPreferences(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });

    it('throws InvalidResponseFormat if data.data_sharing_preferences isnt in response object', async () => {
        mockUserProfileAPI(mockEmptyData, 'get');

        const userProfileAPI = new UserProfileAPI();
        await  userProfileAPI.getDataSharingPreferences(1).catch(error => {
            expect(error).toBeInstanceOf(InvalidResponseFormat);
        });
    });
});

describe('UserProfileAPI->updateDataSharingPreferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockPostDataSharingPreferences, 'post');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.updateDataSharingPreferences(1, "study_level");

        expect(mockPostDataSharingPreferences).toBeCalledWith('profile/1/data_sharing_preferences', { "data": { "questionCode": "study_level" } });
    });

    it('returns valid: true on successful response', async () => {
        mockUserProfileAPI(mockPostDataSharingPreferences, 'post');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingUpdate = await userProfileAPI.updateDataSharingPreferences(1, "study_level");

        expect(dataSharingUpdate).toEqual({ valid: true });
    });

    it('returns valid: false with unknown error on failed response', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'post');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingUpdate = await userProfileAPI.updateDataSharingPreferences(1, "study_level");

        expect(dataSharingUpdate).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false with error on failed response', async () => {
        mockUserProfileAPI(mockErrorResponse, 'post');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingUpdate = await userProfileAPI.updateDataSharingPreferences(1, "study_level");

        expect(dataSharingUpdate).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});

describe('UserProfileAPI->deleteDataSharingPreferences', () => {
    afterEach(() => {
        jest.clearAllMocks();
    });

    it('requests data from correct URL', async () => {
        mockUserProfileAPI(mockDeleteDataSharingPreferences, 'delete');

        const userProfileAPI = new UserProfileAPI();
        await userProfileAPI.deleteDataSharingPreferences(1, "study_level");

        expect(mockDeleteDataSharingPreferences).toBeCalledWith('profile/1/data_sharing_preferences/study_level');
    });

    it('returns valid: true on successful response', async () => {
        mockUserProfileAPI(mockDeleteDataSharingPreferences, 'delete');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingDelete = await userProfileAPI.deleteDataSharingPreferences(1, "study_level");

        expect(dataSharingDelete).toEqual({ valid: true });
    });

    it('returns valid: false with unknown error on failed response', async () => {
        mockUserProfileAPI(mockUnknownErrorResponse, 'delete');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingDelete = await userProfileAPI.deleteDataSharingPreferences(1, "study_level");

        expect(dataSharingDelete).toEqual({ valid: false, error: 'UNKNOWN' });
    });

    it('returns valid: false with error on failed response', async () => {
        mockUserProfileAPI(mockErrorResponse, 'delete');

        const userProfileAPI = new UserProfileAPI();
        const dataSharingDelete = await userProfileAPI.deleteDataSharingPreferences(1, "study_level");

        expect(dataSharingDelete).toEqual({ valid: false, error: 'ERROR_CODE' });
    });
});
