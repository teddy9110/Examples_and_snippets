import User from './';
import UserAPI from './data';

describe('module exports', () => {
    it('returns UserAPI', async () => {
        expect(typeof User.UserAPI).toBe(typeof UserAPI);
    });

    it('returns resolvers', async () => {
        expect(typeof User.resolvers.Query.checkEmail).toBe('function');
        expect(typeof User.resolvers.Query.checkLoggedIn).toBe('function');
        expect(typeof User.resolvers.Query.checkUsername).toBe('function');
        expect(typeof User.resolvers.Query.emailLogin).toBe('function');
        expect(typeof User.resolvers.Query.usernameLogin).toBe('function');
        expect(typeof User.resolvers.Query.socialLogin).toBe('function');
        expect(typeof User.resolvers.Query.user).toBe('function');
        expect(typeof User.resolvers.Query.user_following_list).toBe('function');
        expect(typeof User.resolvers.Query.isFollowing).toBe('function');
        expect(typeof User.resolvers.Mutation.logOut).toBe('function');
        expect(typeof User.resolvers.Mutation.registerUser).toBe('function');
        expect(typeof User.resolvers.Mutation.updateQualifications).toBe('function');
        expect(typeof User.resolvers.Mutation.updateInternationalUserData).toBe('function');
        expect(typeof User.resolvers.Mutation.followUser).toBe('function');
        expect(typeof User.resolvers.Mutation.unfollowUser).toBe('function');
    });

    it('returns typeDef', async () => {
        expect(typeof User.typeDef).toBe('object');
    });
});
