import Lead from '.';
import LeadAPI from './data';
  
describe('module exports', () => {
    it('returns LeadAPI', async () => {
        expect(typeof Lead.LeadAPI).toBe(typeof LeadAPI);
    });

    it('returns resolvers', async () => { 
        expect(typeof Lead.resolvers.Query.lead_list).toBe('function');
        expect(typeof Lead.resolvers.Mutation.lead_create).toBe('function');
        expect(typeof Lead.resolvers.Lead.user).toBe('function');
    });

    it('returns typeDef', async () => { 
        expect(typeof Lead.typeDef).toBe('object');
    });
});
