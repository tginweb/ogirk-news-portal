import {modelOptions, plugin, prop} from "@typegoose/typegoose";

import paginationPlugin from '~lib/db/mongoose/plugins/graphql'

const mongooseProfiler = require('mongoose-profiler');


@plugin(paginationPlugin, {
    defaultSortField: '_id',
    defaultSortAscending: false,
    views: {
        default: ['_id', 'nid', 'parent', 'children', 'name', 'nameShort', 'name_ru', 'name_en', 'title', 'content', 'slug', 'code', 'taxonomy', 'weight', 'meta', 'lastActivity', 'postCount'],
        public: ['_id', 'nid', 'parent', 'children', 'name', 'nameShort', 'name_ru', 'name_en', 'title', 'content', 'slug', 'code', 'taxonomy', 'weight', 'meta', 'lastActivity', 'postCount'],
        public_full: ['_id', 'nid', 'parent', 'children', 'name', 'nameShort', 'name_ru', 'name_en', 'title', 'content', 'field', 'slug', 'code', 'taxonomy', 'weight', 'meta', 'lastActivity', 'postCount'],
    }
})
@plugin(require('mongoose-named-scopes'))
@modelOptions({
    schemaOptions: {
        collection: "term",
        discriminatorKey: "taxonomy",
        toJSON: {virtuals: true},
        toObject: {virtuals: true},
    }
})
export class TermModel {

    _id: number | string;

    @prop()
    nid: number;

    @prop()
    parent: number;

    @prop({})
    name: string;

    @prop({})
    nameShort: string;

    @prop({})
    name_ru: string;

    @prop({})
    name_en: string;

    @prop({})
    title: string;

    @prop({})
    content: string;

    @prop()
    slug: string;

    @prop()
    code: string;

    @prop()
    taxonomy: string;

    @prop()
    weight: number;

    @prop({
        _id: false,
        default: {}
    })
    meta: object;

    @prop({
        _id: false,
        default: {}
    })
    field: object;

    @prop({})
    lastActivity: Date;

    @prop()
    postCount: number;

    @prop({})
    nameLemmas?: string[];

    async getNameLemmasSaved() {
        if (!this.nameLemmas || !this.nameLemmas.length) {
            this.postCount = 5000
            await this['save']()
        }
        return this.nameLemmas
    }

}
