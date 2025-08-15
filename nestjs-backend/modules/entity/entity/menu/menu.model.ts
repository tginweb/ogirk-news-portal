import {modelOptions, plugin, prop} from "@typegoose/typegoose";
import paginationPlugin from '~lib/db/mongoose/plugins/graphql'

@plugin(paginationPlugin, {
    defaultSortField: 'created',
    defaultSortAscending: false,
    views: {
        public: ['_id', 'nid', 'created', 'title'],
        public_full: ['_id', 'nid', 'created', 'title'],
    }
})
@plugin(require('mongoose-named-scopes'))
@modelOptions({
    schemaOptions: {
        collection: "menu"
    }
})
export class MenuModel {

    _id: string;

    @prop({})
    nid: Number;

    @prop({})
    code: string

    @prop({})
    created: number | string | Date;

    @prop({})
    title: string
}
