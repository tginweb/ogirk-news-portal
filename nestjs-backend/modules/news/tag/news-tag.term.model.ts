import {modelOptions} from "@typegoose/typegoose";
import {TermModel} from "~modules/entity/entity/term/term.model";

@modelOptions({
    schemaOptions: {
        collection: "term",
        discriminatorKey: "taxonomy",
    }
})
export class NewsTagTermModel extends TermModel {

    public get url() {
        return '/tag/' + this.slug + '/'
    }
}
