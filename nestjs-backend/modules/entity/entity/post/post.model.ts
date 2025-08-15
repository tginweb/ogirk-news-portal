import {modelOptions, plugin, prop} from "@typegoose/typegoose";

import paginationPlugin from '~lib/db/mongoose/plugins/graphql'
import stripTags from "~lib/util/content/stripTags";

import {PostStatData} from './model/stat'

const mongooseProfiler = require('mongoose-profiler');
const util = require('util')

class ContentWord {
    @prop()
    word: string;

    @prop()
    lemma: string;
}

class ContentTag {
    @prop()
    text: string;

    @prop()
    url: string;

    @prop()
    limit: number;
}


@plugin(paginationPlugin, {
    defaultSortField: 'created',
    defaultSortAscending: false,
    views: {
        public: ['_id', 'nid', 'created', 'menuOrder', 'url', 'slug', 'codePath', 'format', 'title', 'type', 'parentNid', 'excerpt', 'taxonomy', 'file', 'thumb', 'meta', 'stat'],
        public_full: ['_id', 'nid', 'created', 'menuOrder', 'url', 'slug', 'codePath', 'format', 'title', 'type', 'parentNid', 'excerpt', 'taxonomy', 'file', 'thumb', 'meta', 'stat', 'content', 'builder', 'builderType'],
    }
})
@plugin(require('mongoose-named-scopes'))
@modelOptions({
    schemaOptions: {
        collection: "post",
        discriminatorKey: "type",
        toJSON: {
            virtuals: true,
            transform: function (doc, ret) {
                // @ts-ignore
                ret.created = doc.created.getTime()
            }
        },
        toObject: {virtuals: true},
    }
})
export class PostModel {

    _id: string;

    @prop({})
    nid: Number;

    @prop({})
    created: Date;

    @prop()
    menuOrder: Number;

    @prop()
    type: string;

    @prop()
    parentNid: Number;

    @prop()
    format: string

    @prop({})
    title: string

    @prop({})
    slug: string

    @prop({})
    codePath: string

    @prop({})
    content: string

    @prop({})
    builder: JSON

    @prop({})
    builderType: string

    @prop({})
    excerpt: string

    @prop()
    taxonomy: object

    @prop()
    meta: object

    @prop()
    thumb: object

    @prop()
    file: object

    @prop()
    queryId: string


    @prop({graph: true, depFields: ['taxonomy']})
    terms: object

    @prop({graph: true, depFields: ['taxonomy']})
    termsByTax: object

    @prop({graph: true, depFields: ['thumb']})
    image: object


    @prop({_id: false, default: {_hook: true}})
    stat: PostStatData;

    @prop()
    indexStatus: string;

    @prop({_id: false, type: ContentWord})
    contentWords?: ContentWord[]

    @prop({_id: false, type: ContentTag})
    contentTags?: ContentTag[]
}

