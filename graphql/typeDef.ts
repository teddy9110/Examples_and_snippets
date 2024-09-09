import { gql } from 'apollo-server-core';
import Announcement from './announcement';
import Attachment from './attachment';
import CMS from './cms';
import Education from './education';
import Forum from './forum';
import Lead from './lead';
import Messages from './messages';
import Notification from './notification';
import Poll from './poll';
import Reputation from './reputation';
import Search from './search';
import User from './user';
import Representatives from './representatives';

// Base type definition for our graph
const typeDef = gql`
    enum CacheControlScope {
        PUBLIC
        PRIVATE
    }

    directive @cacheControl(
        maxAge: Int
        scope: CacheControlScope
        inheritMaxAge: Boolean
    ) on FIELD_DEFINITION | OBJECT | INTERFACE | UNION

    input KeyValue {
        key: String!
        value: String!
    }

    type ValidityCheck {
        valid: Boolean!,
        error: String
    }

    type UpdateResult {
        success: Boolean!
        error: String
    }

    # The "Query" type is special: it lists all of the available queries that
    # clients can execute, along with the return type for each.
    type Query

    type Mutation
`;

export default [
    typeDef,
    Announcement.typeDef,
    Attachment.typeDef,
    CMS.typeDef,
    Education.typeDef,
    Forum.typeDef,
    Lead.typeDef,
    Messages.typeDef,
    Notification.typeDef,
    Poll.typeDef,
    Reputation.typeDef,
    Search.typeDef,
    User.typeDef,
    Representatives.typeDef,
];
