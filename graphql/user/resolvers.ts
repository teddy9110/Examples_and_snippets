import { User, UserResults, UserType, UserTypeResults, NewUser, AccessTokens, UserProfile, MarketingPreference, IgnoredUsersList, UserProfileDataSharingPreference, RecommendedUser } from './types';
import { ValidityCheck } from '../types';
import { Post } from '../forum/types';
import { setAccessTokenCookies, clearAccessTokens } from '../tokens';
import { NotLoggedIn, NoClientId } from '../errors';
import createLoaders from '../dataLoaders';

export default {
    Query: {
        user(parent: any, args: any, context: any, info: any): Promise<User> {
            const loaders: any = createLoaders(context.dataSources);
            return loaders.users.load(args.id);
        },
        async user_list(parent: any, args: any, context: any, info: any): Promise<UserResults> {
            const users = await context.dataSources.userAPI.getList(args.page, args.per_page, args.order_by, args.order_by_direction, args.filters);
            return {
                users: users.data,
                total_results: users.total_results
            }
        },
        user_type(parent: any, args: any, context: any, info: any): Promise<UserType> {
            return context.dataSources.userTypeAPI.getSingle(args.id);
        },
        async user_type_list(parent: any, args: any, context: any, info: any): Promise<UserTypeResults> {
            const user_types = await context.dataSources.userTypeAPI.getList(args.page, args.per_page, args.order_by, args.order_by_direction, args.filters);
            return {
                user_types: user_types.data,
                total_results: user_types.total_results
            }
        },
        async user_type_many(parent: any, args: any, context: any, info: any): Promise<[UserType]> {
            const loaders: any = createLoaders(context.dataSources);
            return await loaders.user_types.loadMany(args.ids);
        },
        user_following_list(parent: any, args: any, context: any, info: any): Promise<[User]> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.reject(new NotLoggedIn('Not logged in'));
            }
            return context.dataSources.userAPI.getFollowing(args.id, args.page, args.per_page);
        },
        async emailLogin(parent: any, args: any, context: any, info: any): Promise<AccessTokens> {
            let parsedSource = args.source;
            parsedSource.additional_data = JSON.parse(parsedSource.additional_data);
            const tokens = await context.dataSources.userAPI.getAccessTokenFromEmail(args.email, args.password, parsedSource);
            setAccessTokenCookies(context.req, context.res, tokens.access_token, tokens.refresh_token);
            return tokens;
        },
        async usernameLogin(parent: any, args: any, context: any, info: any): Promise<AccessTokens> {
            let parsedSource = args.source;
            parsedSource.additional_data = JSON.parse(parsedSource.additional_data);
            const tokens = await context.dataSources.userAPI.getAccessTokenFromUsername(args.username, args.password, parsedSource);
            setAccessTokenCookies(context.req, context.res, tokens.access_token, tokens.refresh_token);
            return tokens;
        },
        async socialLogin(parent: any, args: any, context: any, info: any): Promise<AccessTokens> {
            let parsedSource = args.source;
            parsedSource.additional_data = JSON.parse(parsedSource.additional_data);
            const tokens = await context.dataSources.userAPI.getAccessTokenFromSocial(args.provider, args.access_token, parsedSource);
            setAccessTokenCookies(context.req, context.res, tokens.access_token, tokens.refresh_token);
            return tokens;
        },
        checkEmail(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            return context.dataSources.userAPI.checkEmail(args.email);
        },
        checkUsername(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            return context.dataSources.userAPI.checkUsername(args.username);
        },
        checkLoggedIn(parent: any, args: any, context: any, info: any): any {
            return context.res.getHeader('X-USERID') ? parseInt(context.res.getHeader('X-USERID')) : 0;
        },
        userProfile(parent: any, args: any, context: any, info: any): Promise<UserProfile> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.reject(new NotLoggedIn('Not logged in'));
            }
            return context.dataSources.userProfileAPI.getProfile(userId);
        },
        userIgnoreList(parent: any, args: any, context: any, info: any): Promise<IgnoredUsersList> {
            return context.dataSources.userAPI.getUserIgnoreList(args.page, args.per_page, args.order_by, args.order_by_direction, args.filters);
        },
        userMarketingPreferences(parent: any, args: any, context: any, info: any): Promise<[MarketingPreference]> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.reject(new NotLoggedIn('Not logged in'));
            }
            return context.dataSources.userProfileAPI.getMarketingPreferences(userId);
        },
        async user_data_sharing_preferences(parent: any, args: any, context: any, info: any): Promise<UserProfileDataSharingPreference> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.reject(new NotLoggedIn('Not logged in'));
            }

            return context.dataSources.userProfileAPI.getDataSharingPreferences(userId);
        },
        userSegments(parent: any, args: any, context: any, info: any): Promise<any> {
            const cookies = context.req.cookies;
            const clientId = '_tsr' in cookies ? cookies['_tsr'] : null;
            if (!clientId) {
                return Promise.reject(new NoClientId('No ClientID Cookie'));
            }
            return context.dataSources.marketingAPI.getUserSegments(clientId);
        },
        recommendedUsers(parent: any, args: any, context: any, info: any): Promise<RecommendedUser[]> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.reject(new NotLoggedIn('Not logged in'));
            }
            return context.dataSources.userAPI.getRecommendedUsers(userId);
        },
        isFollowing(parent: any, args: any, context: any, info: any): Boolean {
            return context.dataSources.userAPI.isFollowing(args.user_id);
        },
    },
    Mutation: {
        async registerUser(parent: any, args: any, context: any, info: any): Promise<NewUser> {
            let parsedArgs = args;
            parsedArgs.source.additional_data = JSON.parse(parsedArgs.source.additional_data);
            const newUser = await context.dataSources.userAPI.registerUser(parsedArgs);
            setAccessTokenCookies(context.req, context.res, newUser.tokens.access_token, newUser.tokens.refresh_token);
            return newUser;
        },
        async updateProfile(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateProfile(userId, args);
        },
        async updateSubjects(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateSubjects(userId, args.subject_ids, args.stage);
        },
        async updateLearningProviders(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateLearningProviders(userId, args.learning_provider_ids, args.stage);
        },
        // Deprecated in favour of updateQualificationStage
        async updateQualifications(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateQualifications(userId, args.qualifications.qualification_ids);
        },
        // End deprecated
        async updateUserQualification(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateUserQualification(args.id, args.data);
        },
        async updateQualificationStage(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateQualificationStage(userId, args.qualifications, args.stage);
        },
        // Deprecated in favour of marketing_preferences_update
        async updateMarketingPreferences(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateMarketingPreferences(userId, args.data);
        },
        // End Deprecated
        async marketing_preferences_update(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            let params = args.preferences.map((preference: any) => {
                return {
                    code: preference.code,
                    value: preference.frequency
                };
            });
            return context.dataSources.userProfileAPI.updateMarketingPreferences(userId, params);
        },
        async topics_of_interest_update(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateTopicsOfInterest(userId, args.interested_topic_codes, args.uninterested_topic_codes);
        },
        async marketing_preferences_email_unsubscribe(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            return context.dataSources.userProfileAPI.unsubscribeEmail(args.id, args.marketing_preferences_code);
        },
        async updateInternationalUserData(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }
            return context.dataSources.userProfileAPI.updateInternationalUserData(userId, args);
        },
        async confirmEmail(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            return context.dataSources.userAPI.confirmEmail(args.email, args.token);
        },
        async sendEmailConfirmationLink(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            return context.dataSources.userAPI.sendEmailConfirmationLink(args.email);
        },
        async user_data_sharing_preferences_update(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }

            return context.dataSources.userProfileAPI.updateDataSharingPreferences(userId, args.questionCode);
        },
        async user_data_sharing_preferences_delete(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false, error: 'NOT_LOGGED_IN' });
            }

            return context.dataSources.userProfileAPI.deleteDataSharingPreferences(userId, args.questionCode);
        },
        logOut(parent: any, args: any, context: any, info: any): Boolean {
            clearAccessTokens(context.res);
            return true;
        },
        user_update(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            let userData = {};
            if (args.user_type_id) {
                userData['user_type_id'] = args.user_type_id;
            }

            if (!Object.keys(userData).length) {
                return Promise.resolve({ valid: false, error: 'NO_DATA' });
            }

            return context.dataSources.userAPI.update(args.id, userData);
        },
        dismissFollowSuggestion(parent: any, args: any, context: any, info: any): Promise<ValidityCheck> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.resolve({ valid: false });
            }
            return context.dataSources.userAPI.dismissFollowSuggestion(userId, args.user_id);
        },
        followUser(parent: any, args: any, context: any, info: any): Boolean {
            return context.dataSources.userAPI.followUser(args.user_id);
        },
        unfollowUser(parent: any, args: any, context: any, info: any): Boolean {
            return context.dataSources.userAPI.unfollowUser(args.user_id);
        },
    },
    User: {
        latestPosts(parent: any, args: any, context: any, info: any): Promise<Post[]> {
            return context.dataSources.forumAPI.getPosts(1, 5, 'date', 'desc', [{
                key: 'user_id',
                value: parent.id,
            }]);
        },
    },
    RecommendedUser: {
        latestPosts(parent: any, args: any, context: any, info: any): Promise<Post[]> {
            return context.dataSources.forumAPI.getPosts(1, 5, 'date', 'desc', [{
                key: 'user_id',
                value: parent.id,
            }], true); // include_forums
        },
    },
    UserProfile: {
        async email(parent: any, args: any, context: any, info: any): Promise<string> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return '';
            }

            const loaders: any = createLoaders(context.dataSources);
            const user = await loaders.users.load(userId);
            return user.email ?? '';
        },
        async user_data_sharing_preferences(parent: any, args: any, context: any, info: any): Promise<UserProfileDataSharingPreference> {
            const userId = context.res.getHeader('X-USERID') ?? 0;
            if (!userId) {
                return Promise.reject(new NotLoggedIn('Not logged in'));
            }

            return context.dataSources.userProfileAPI.getDataSharingPreferences(userId);
        }
    }
};
