import {modelOptions} from "@typegoose/typegoose";
import {PostModel} from "~modules/entity/entity/post/post.model";

@modelOptions({
    schemaOptions: {
        collection: "post",
        discriminatorKey: "type",
    }
})
export class NewsQuizPostModel extends PostModel {

    public get url() {
        return '/quiz/' + this.slug
    }
}
