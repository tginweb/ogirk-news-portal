import {modelOptions} from "@typegoose/typegoose";
import {PostModel} from "~modules/entity/entity/post/post.model";

@modelOptions({
    schemaOptions: {
        collection: "post",
        discriminatorKey: "type",
    }
})
export class NewsPostPostModel extends PostModel {

    public get url() {
        let date = new Date(this.created)

        let month = (date.getUTCMonth() + 1).toString()
        let day = date.getUTCDate().toString()

        if (month.length == 1)
            month = '0' + month

        if (day.length == 1)
            day = '0' + day

        let suff = ''

        if (this.meta['er_token']) {
            suff = '?erid=' + this.meta['er_token']
        }

        //if (this.url && (this.url.indexOf('http://') === 0 || this.url.indexOf('https://') === 0)) {
        //    return this.url
        //}

        return '/' + date.getFullYear() + '/' + month + '/' + day + '/' + this.slug + '/' + suff
    }
}
