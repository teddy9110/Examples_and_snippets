import TSRApiRESTDataSource from '../TSRApiRESTDataSource';
import {
    User, UserSource, AccessTokens, RegisterUserParams, UserProfileUpdate, UserProfileQualificationsUpdate,
    UserProfileInternationalUpdate, IgnoredUsersList, UpdateQualification, CreateQualification, RecommendedUser
} from './types';
import { InvalidResponseFormat } from '../errors';
import { KeyValue, ValidityCheck } from '../types';

export interface ClientInfo {
    [name: string]: string;
}

export class UserTypeAPI extends TSRApiRESTDataSource {
    constructor(context?: any) {
        // Always call super()
        super(context);
        // Sets the base URL for the REST API
        this.baseURL = process.env.USER_API_DOMAIN;
        this.resourceName = 'user_type';
    }
}

export default class UserAPI extends TSRApiRESTDataSource {
    constructor(context?: any) {
        // Always call super()
        super(context);
        // Sets the base URL for the REST API
        this.baseURL = process.env.USER_API_DOMAIN;
        this.resourceName = 'user';
    }

    async requestUser(userId: number): Promise<User> {
        const timeout = this.getTimeout('user_ignore-list');

        const url = `user/${encodeURIComponent(userId.toString())}`;
        return this.get(url, null, { timeout }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async getFollowing(
        userId: number,
        page: number = 1,
        per_page: number = 5
    ): Promise<[User]> {
        const timeout = this.getTimeout('user_ignore-list');

        const queryString = new URLSearchParams({
            page: page.toString(),
            per_page: per_page.toString()
        });

        const url = `user/${encodeURIComponent(userId.toString())}/following?${queryString.toString()}`;
        return this.get(url, null, { timeout }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async getUserIgnoreList(
        page: number = 1,
        per_page: number = 5,
        order_by: string = 'date',
        order_by_direction: string = 'desc',
        filters: KeyValue[] = []
    ): Promise<IgnoredUsersList> {
        const timeout = this.getTimeout('user_follow-list');

        const queryString = new URLSearchParams({
            page: page.toString(),
            per_page: per_page.toString(),
            order_by,
            order_by_direction
        });

        filters.forEach(filter => {
            queryString.append(filter.key, filter.value);
        });

        const url = `user/ignore/list${queryString.toString() != '' ? '?' : ''}${queryString.toString()}`;

        return this.get(url, null, { timeout }).then(response => {
            if (typeof response?.data?.ignoredUsers !== 'undefined') {
                return {
                    ignoredUsers: response.data.ignoredUsers,
                    total_results: response?.data?.total_results ?? 0
                }
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data.ignoredUsers'`));
        });
    }

    async getAccessTokenFromEmail(email: string, password: string, source: UserSource): Promise<AccessTokens> {
        const url = `token`;
        return this.post(url, { data: { email, password, source } }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async getAccessTokenFromUsername(username: string, password: string, source: UserSource): Promise<AccessTokens> {
        const url = `token`;
        return this.post(url, { data: { username, password, source } }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async getAccessTokenFromSocial(provider: string, access_token: string, source: UserSource): Promise<AccessTokens> {
        const url = `token`;
        return this.post(url, { data: { social: { provider, access_token }, source } }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async getAccessTokenFromTSRCookies(user_id: number, password: string, session_hash: string) {
        const url = `legacy/autologin`;
        return this.post(url, { data: { user_id, password, session_hash } }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async refreshAccessToken(refresh_token: string, user_id: number, clientInfo: ClientInfo | undefined = undefined) {
        const url = `access_token`;
        return this.post(url, { data: { refresh_token, user_id } }, (clientInfo ? { headers: clientInfo } : undefined)).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async checkUsername(username: string) {
        const url = `user/usernamecheck/${encodeURIComponent(username)}`;
        return this.get(url)
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }

    async checkEmail(email: string) {
        const url = `user/emailcheck/${encodeURIComponent(email)}`;
        return this.get(url)
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async registerUser(data: RegisterUserParams) {
        const url = `user`;

        return this.post(url, { data }).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        });
    }

    async confirmEmail(email: string, token: string) {
        const url = `user/email/confirm`;
        return this.put(url, { data: { email, token } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async sendEmailConfirmationLink(email: string) {
        const url = `user/email/confirm`;
        return this.post(url, { data: { email } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async getRecommendedUsers(userId: number): Promise<RecommendedUser[]> {
        const url = `user/${userId}/recommend`;
        return this.get(url).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response.data;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserAPI ${url} is missing 'data'`));
        })
    }

    async dismissFollowSuggestion(userId: number, dismissUserId: number): Promise<ValidityCheck> {
        const url = `user/${userId}/dismiss-recommendation`;
        return this.post(url, { userId: dismissUserId })
            .then(() => { return { valid: true } })
            .catch(() => { return { valid: false } });
    }

    async isFollowing(userid: number): Promise<Boolean> {
        const url = `user/following/${userid}`;
        return this.get(url)
            .then(response => {
                return response.following;
            })
            .catch(() => { return false })
    }

    async followUser(userid: number): Promise<Boolean> {
        const url = `user/follow/${userid}`;
        return this.put(url)
            .then(() => { return true })
            .catch(() => { return false })
    }

    async unfollowUser(userid: number): Promise<Boolean> {
        const url = `user/follow/${userid}`;
        return this.delete(url)
            .then(() => { return true })
            .catch(() => { return false })
    }
}

export class UserProfileAPI extends TSRApiRESTDataSource {
    constructor() {
        // Always call super()
        super();
        // Sets the base URL for the REST API
        this.baseURL = process.env.USERPROFILE_API_DOMAIN;
    }

    async getProfile(userId: number) {
        const url = `profile/${encodeURIComponent(userId.toString())}`;

        return this.get(url).then(response => {
            if (typeof response?.data?.user_profile !== 'undefined') {
                return response?.data?.user_profile;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserProfileAPI ${url} is missing 'data'`));
        });
    }

    async getMarketingPreferences(userId: number) {
        const url = `profile/${encodeURIComponent(userId.toString())}/marketing_preferences`;

        return this.get(url).then(response => {
            if (typeof response?.data !== 'undefined') {
                return response?.data
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserProfileAPI ${url} is missing 'data'`));
        });
    }

    async getDataSharingPreferences(userId: number){
        const url = `profile/${encodeURIComponent(userId.toString())}/data_sharing_preferences`;

        return this.get(url).then(response => {
            if (typeof response?.data?.data_sharing_preferences !== 'undefined') {
                return response?.data?.data_sharing_preferences;
            }

            return Promise.reject(new InvalidResponseFormat(`Response from UserProfileAPI ${url} is missing 'data.data_sharing_preferences'`));
        });
    }

    // qualification update is deprecated use Update qualifications if the qualification already exists 
    async updateProfile(userId: number, data: UserProfileUpdate) {
        const url = `profile/${encodeURIComponent(userId.toString())}`;

        return this.patch(url, { data })
            .then(response => { return { valid: true } })
            .catch(err => {
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async updateSubjects(userId: number, subject_ids: number[], stage: string) {
        const url = `profile/${encodeURIComponent(userId.toString())}/subject/${encodeURIComponent(stage)}`;

        return this.put(url, { data: { subject_ids } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async updateMarketingPreferences(userId: number, data: KeyValue[]) {
        const url = `profile/${encodeURIComponent(userId.toString())}/marketing_preferences`;
        return this.put(url, { data })
            .then(response => { return { valid: true, } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }

    async updateTopicsOfInterest(userId: number, interested_topic_codes: string[], uninterested_topic_codes: string[]) {
        const url = `profile/${encodeURIComponent(userId.toString())}/topics`;
        return this.put(url, { data: { interested_topic_codes, uninterested_topic_codes } })
            .then(response => { return { valid: true, } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }

    async updateUserQualification(id: number, data: UpdateQualification) {
        const url = `profile/qualifications/${id}`;
        return this.patch(url, { data })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }


    // Deprecated in favour of updateQualificationStage
    async updateQualifications(userId: number, qualification_ids: UserProfileQualificationsUpdate[]) {
        const url = `profile/${encodeURIComponent(userId.toString())}/qualifications`;
        return this.patch(url, { data: { qualification_ids } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }
    // End deprecated

    async updateQualificationStage(userId: number, qualifications: CreateQualification[], stage: string) {
        const url = `profile/${encodeURIComponent(userId.toString())}/qualifications/${stage}`;
        return this.put(url, { data: { qualifications } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }

    async updateLearningProviders(userId: number, learning_provider_ids: number[], stage: string) {
        const url = `profile/${encodeURIComponent(userId.toString())}/learning_provider/${encodeURIComponent(stage)}`;

        return this.put(url, { data: { learning_provider_ids } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }

    async updateInternationalUserData(userId: number, data: UserProfileInternationalUpdate) {
        const url = `profile/${encodeURIComponent(userId.toString())}/international`;
        return this.patch(url, { data: data })
            .then(response => { return { valid: true } })
            .catch(err => {
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async unsubscribeEmail(userId: string, marketing_preferences_code: string) {
        const url = `profile/marketing_preferences/unsubscribe`;
        return this.put(url, { data: { user_id: userId, marketing_preferences_code } })
            .then(response => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }

                return { valid: false, error: errorCode }
            });
    }

    async updateDataSharingPreferences(userId: number, questionCode: string) {
        const url = `profile/${encodeURIComponent(userId.toString())}/data_sharing_preferences`;
        return this.post(url, { data: { questionCode } })
            .then(() => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }

    async deleteDataSharingPreferences(userId: number, questionCode: string) {
        const url = `profile/${encodeURIComponent(userId.toString())}/data_sharing_preferences/${encodeURIComponent(questionCode)}`;
        return this.delete(url)
            .then(() => { return { valid: true } })
            .catch(err => {
                console.log(err);
                let errorCode = 'UNKNOWN';
                // If there's a genuine error returned, expose this in the error field
                if (typeof err?.extensions?.response?.body?.errors[0]?.code !== 'undefined') {
                    errorCode = err.extensions.response.body.errors[0].code;
                }
                return { valid: false, error: errorCode }
            });
    }
}
