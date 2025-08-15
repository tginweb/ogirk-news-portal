import {modelOptions, prop} from "@typegoose/typegoose";

@modelOptions({
    schemaOptions: {
        collection: "entity_stat",
    }
})
export class EntityStatModel {

    _id: string;

    @prop()
    entityType: string;

    @prop()
    entityNid: number;

    @prop({default: 0})
    views: number;

    @prop({default: false})
    ported: boolean;
}


