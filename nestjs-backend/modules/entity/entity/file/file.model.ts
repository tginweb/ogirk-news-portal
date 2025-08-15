import {modelOptions, plugin, prop} from "@typegoose/typegoose";
import paginationPlugin from '~lib/db/mongoose/plugins/graphql'

const autoincrement = require('simple-mongoose-autoincrement');

@plugin(paginationPlugin, {
    defaultSortField: '_id',
    defaultSortAscending: false,
    views: {
        default: [],
        public: [],
    }
})
@plugin(require('mongoose-named-scopes'))
@plugin(autoincrement, {field: 'nid'})
@modelOptions({
    schemaOptions: {
        collection: "file",
        toJSON: {virtuals: true},
        toObject: {virtuals: true},
    }
})
export class FileModel {

    _id: string;

    @prop({})
    mimetype: string;

    @prop({})
    originalname: string;

    @prop({})
    filename: string;

    @prop({})
    filesize: number;

    @prop({})
    relDocType: string;

    @prop({})
    relDocId: string;

    @prop({})
    relDocPath: string;

    @prop({})
    relSubdocId: string;


    @prop({})
    name: string;

    @prop()
    content: string;

    public get downloadUrl() {
        return '/api/file/download?id=' + this._id
    }
}
