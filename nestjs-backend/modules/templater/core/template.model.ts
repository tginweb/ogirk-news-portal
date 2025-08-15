import {modelOptions, plugin, prop} from "@typegoose/typegoose";
import {BaseModel} from '~lib/db/typegoose/base.model'
import paginationPlugin from "~lib/db/mongoose/plugins/graphql";

@plugin(paginationPlugin, {
    defaultSortField: 'createAt',
    defaultSortAscending: false,
    views: {

    }
})
@plugin(require('mongoose-named-scopes'))
@modelOptions({schemaOptions: {collection: "template"}})
export class TemplateModel extends BaseModel {

    _id: number | string;

    @prop({})
    name: string;

    @prop({})
    format: string; // hbs | pug

    @prop({})
    content: string;
}
