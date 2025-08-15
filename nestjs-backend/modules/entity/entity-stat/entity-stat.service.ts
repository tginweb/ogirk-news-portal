import {Injectable} from '@nestjs/common';
import {InjectModel} from "nestjs-typegoose";
import {EntityStatModel} from "./entity-stat.model";
import {PostModel} from "./../entity/post/post.model";
import {TermModel} from "./../entity/term/term.model";

import {ReturnModelType} from "@typegoose/typegoose";


@Injectable()
export class EntityStatService {

    constructor(
        @InjectModel(EntityStatModel) public readonly entityStatModel: ReturnModelType<typeof EntityStatModel>,
        @InjectModel(PostModel) public readonly entityPostModel: ReturnModelType<typeof PostModel>,
        @InjectModel(TermModel) public readonly entityTermModel: ReturnModelType<typeof TermModel>,
    ) {

    }

    async portStat() {

        let docs = await this.entityStatModel.find({
             ported: false
        }).exec()

        console.log(docs)

        docs.forEach((doc) => {

            let model;


            switch (doc.entityType) {
                case 'post':
                    model = this.entityPostModel
                    break;
                case 'term':
                    model = this.entityTermModel
                    break;
            }

            model.findOneAndUpdate(
                {
                    nid: doc.entityNid
                },
                {
                    "stat.views": doc.views
                },
                {},
                () => {}
            )

            this.entityStatModel.findOneAndUpdate(
                {
                    _id: doc._id
                },
                {
                    ported: true
                }
            ).exec()
        })
    }
}
