import {modelOptions} from "@typegoose/typegoose";
import {PostModel} from "~modules/entity/entity/post/post.model";

@modelOptions({
    schemaOptions: {
        collection: "post",
        discriminatorKey: "type",
    }
})
export class NewsHubPostModel extends PostModel {
    public get url() {
        return '/hub/' + this.slug + '/'
    }
}
