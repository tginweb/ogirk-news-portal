import {modelOptions} from "@typegoose/typegoose";
import {PostModel} from "~modules/entity/entity/post/post.model";

@modelOptions({
    schemaOptions: {
        collection: "post",
        discriminatorKey: "type",
    }
})
export class NewsIssuePostModel extends PostModel {

    public get url() {
        let date = new Date(this.created)
        return '/issue-print/' + this.slug + '/'
    }
}
