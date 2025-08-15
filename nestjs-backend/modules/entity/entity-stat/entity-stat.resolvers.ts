import {Args, Info, Mutation, Resolver} from '@nestjs/graphql';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {EntityStatModel} from "./entity-stat.model"
import {PostService} from "~modules/entity/entity/post/post.service";

@Resolver('EntityStat')
export class EntityStatResolvers {

    constructor(
        @InjectModel(EntityStatModel) private readonly entityStatModel: ReturnModelType<typeof EntityStatModel> | any,
        private readonly postService: PostService,
    ) {

    }


    async entityStat_HitView1(
        @Args() args,
        @Info() info,
    ) {
        let entity

        switch (args.entityType) {
            case 'post':
                entity = await this.postService.postModel.findOneAndUpdate({
                    nid: args.entityNid
                }, {
                    $inc : {'stat.views' : 1}
                }).exec()
                break;
        }

        return 'ok'
    }

    @Mutation()
    async entityStat_HitView(
        @Args() args,
        @Info() info,
    ) {
        let entity = await this.entityStatModel.findOne({
            entityType: args.entityType,
            entityNid: args.entityNid
        }).exec()

        if (!entity) {
            entity = new this.entityStatModel({
                entityType: args.entityType,
                entityNid: args.entityNid,
                views: 0
            })
        }

        entity.ported = false
        entity.views++

        await entity.save();
        return 'ok'
    }

}
