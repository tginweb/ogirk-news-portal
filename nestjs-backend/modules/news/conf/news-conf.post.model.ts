import {modelOptions} from "@typegoose/typegoose";
import {PostModel} from "~modules/entity/entity/post/post.model";

@modelOptions({
    schemaOptions: {
        collection: "post",
        discriminatorKey: "type",
    }
})
export class NewsConfPostModel extends PostModel {

    public get url() {
        return '/conf/' + this.slug + '/'
    }
}
