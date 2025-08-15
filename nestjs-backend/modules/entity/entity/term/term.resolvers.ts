import {Args, Info, Query, Resolver, ResolveField, Subscription, Parent, Root, CONTEXT, Context} from '@nestjs/graphql';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {
    TermModel,
    TermService,
} from "./index"
import {
    PostModel,
    PostService
} from "../post";


@Resolver('Term')
export class TermResolvers {

    constructor(
        @InjectModel(TermModel) private readonly termModel: ReturnModelType<typeof TermModel> | any,
        private readonly termService: TermService,
        private readonly postService: PostService,
    ) {

    }

    @Query()
    async getTermsList(
        @Args() args,
        @Info() info,
    ) {
        const result = await this.termService.find({input: args, pager: 'cursor'});
        return result.nodes
    }

    @Query()
    async getTerms(
        @Args() args,
        @Info() info,
    ) {
        return this.termService.find({input: args, pager: 'cursor'});
    }

    @Query()
    async getTerm(
        @Args() args,
        @Info() info,
    ) {
        return this.termService.findOne({input: args});
    }

    @ResolveField()
    async post(
        @Parent() parent,
        @Args() args,
        @Info() info,
    ) {
        args.filter = {
            ...args.filter,
            tax: [
                {taxonomy: parent.taxonomy, id: parent.nid}
            ]
        }
        return this.postService.findOne({input: args});
    }

    @ResolveField()
    async posts(
        @Parent() parent,
        @Args() args,
        @Info() info,
    ) {
        args.filter = {
            ...args.filter,
            tax: [
                {taxonomy: parent.taxonomy, id: parent.nid}
            ]
        }

        return this.postService.find({input: args});
    }

    @ResolveField()
    async editUrl(
        @Args() args,
        @Parent() parent,
        @Info() info
    ) {
        return '/wp-admin/term.php?taxonomy=category&tag_ID=' + parent.nid
    }

    @ResolveField()
    async nameLemmasList(
        @Args() args,
        @Parent() parent,
        @Info() info
    ) {
        return parent.getNameLemmasSaved()
    }
}

