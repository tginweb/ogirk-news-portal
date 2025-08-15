import {Args, Context, Info, Mutation, Parent, Query, ResolveField, Resolver} from '@nestjs/graphql';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {PostModel, PostService} from "./index"
import {Loader} from "~lib/interceptors/dataloader";
import * as DataLoader from "dataloader";
import wpautop from "~lib/util/content/wpautop";
import contentFormat from "~lib/util/content/format";

import {resolverQueryEnd, resolverQueryStart} from '~lib/graphql/util'

import {TermLoader} from "../term/term.dataloader"
import {TermService} from "../term/term.service"
import {TermModel} from "../term/term.model"
import {PostLoader} from "./post.dataloader"

import {ImagingService} from "~modules/imaging/imaging.service";
import {InjectConfig} from "nestjs-config";

const moment = require("moment");

async function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


@Resolver('Post')
export class PostResolvers {

    constructor(
        @InjectModel(PostModel) private readonly postModel: ReturnModelType<typeof PostModel> | any,
        private readonly postService: PostService,
        private readonly termService: TermService,
        private readonly imagingService: ImagingService,
        @InjectConfig() private readonly config,
    ) {

    }

    @Query()
    async getPostsElastic(
        @Args() args,
        @Context() ctx: any,
    ) {
        return await this.postService.findElastic({input: args, pager: 'pages'})
    }

    @Query()
    async getPostsCalendar(
        @Args() args,
        @Context() ctx: any,
    ) {
        const groups = await this.postService.findCalendar({input: args})

        return groups.map(item => ({
            info: item._id,
            count: item.count
        }))
    }

    @Query()
    async getPosts(
        @Args() args,
        @Context() ctx: any,
    ) {

        await resolverQueryStart(ctx, args, 'post');

        let result = await this.postService.find<PostModel>({input: args, pager: 'cursor'});

        resolverQueryEnd(ctx, args, 'post', result.pageInfo.ids);

        return result
    }

    @Query()
    async getPost(
        @Args() args,
    ) {
        return this.postService.findOne<PostModel>({input: args});
    }

    @Query()
    async getPostFull(
        @Args() args,
    ) {
        return this.postService.findOne<PostModel>({input: args, view: 'public_full'});
    }

    @ResolveField()
    async excerpt(
        @Parent() parent,
        @Info() info
    ) {
        return parent.excerpt
    }

    @ResolveField()
    async contentFormatted(
        @Parent() parent,
        @Info() info
    ) {
        return contentFormat(parent.content ? wpautop(parent.content, true) : '')
    }

    @ResolveField()
    async share(
        @Args() args,
        @Parent() parent,
        @Info() info,
    ) {
        let res: any = {};
        res.url = this.config.get('app.SITE_URL_PROD') + parent.url
        if (parent.thumb) {
            const thumbs = this.imagingService.getImageStyles('uploads', parent.thumb.src, ['t1.33'], parent.thumb)
            res.image = thumbs['t1.33'].src
        }
        return res
    }

    @ResolveField()
    async gallery(
        @Args() args,
        @Parent() parent,
        @Info() info,
    ) {
        const gallery = parent.meta.image_gallery || []
        if (parent.thumb)
            gallery.unshift(parent.thumb)
        return gallery
    }

    @ResolveField()
    async terms(
        @Args() args,
        @Parent() parent,
        @Loader(TermLoader) termLoader: DataLoader<TermModel["nid"], TermModel>,
        @Info() info
    ) {
        let ids = {};
        if (parent.taxonomy) {
            let taxonomies = args.tax ? args.tax : Object.keys(parent.taxonomy)
            taxonomies.forEach((taxonomy) => {
                if (parent.taxonomy[taxonomy])
                    for (let id of parent.taxonomy[taxonomy])
                        ids[id] = id
            })
        }
        return this.termService.loadMultiple('id', Object.values(ids))
    }

    @ResolveField()
    async termsByTax(
        @Parent() parent,
        @Loader(TermLoader.name) termLoader: DataLoader<TermModel["nid"], TermModel>,
        @Info() info
    ) {
        let result = {}, ids = {};

        if (parent.taxonomy) {

            for (let [tax, terms] of Object.entries<[number]>(parent.taxonomy)) {
                ids = {}
                for (let id of terms)
                    ids[id] = {id: id, view: 'public', set: {taxonomy: tax}}

                result[tax] = termLoader.loadMany(Object.values(ids))
            }
        }

        return new Promise(async (resolve, reject) => {

            let res = {}

            for (let tax in parent.taxonomy)
                res[tax] = await result[tax]

            resolve(res)
        })
    }


    @ResolveField()
    async image(
        @Args() args,
        @Parent() parent,
        @Info() info
    ) {
        return parent.thumb
    }

    @ResolveField()
    async hub(
        @Parent() parent,
        @Args() args,
        @Info() info,
        @Loader(PostLoader.name) postLoader: DataLoader<any, PostModel>,
    ) {
        const termsHub = parent.taxonomy['sm-hub-term'];
        if (termsHub && termsHub.length) {
            return await this.postService.findOne({
                input: {
                    filter: {
                        type: 'sm-hub-post',
                        tax: [{taxonomy: "sm-hub-term", id: termsHub[0]}]
                    }
                }
            })
        }
    }

    @ResolveField()
    async editUrl(
        @Args() args,
        @Parent() parent,
        @Info() info
    ) {
        return '/wp-admin/post.php?post=' + parent.nid + '&action=edit'
    }

    @ResolveField()
    async textAuthor(
        @Parent() parent,
        @Args() args,
        @Info() info,
        @Loader(TermLoader.name) termLoader: DataLoader<TermModel["nid"], TermModel>,
    ) {
        return parent.meta.sm_author_spr && parent.meta.sm_author_spr.length ? termLoader.loadMany(parent.meta.sm_author_spr.map((nid) => ({
            id: nid,
            view: 'public'
        }))) : []
    }

    @ResolveField()
    async fotoAuthor(
        @Parent() parent,
        @Args() args,
        @Info() info,
        @Loader(TermLoader.name) termLoader: DataLoader<TermModel["nid"], TermModel>,
    ) {
        return parent.meta.sm_authorfoto_spr && parent.meta.sm_authorfoto_spr.length ? termLoader.loadMany(parent.meta.sm_authorfoto_spr.map((nid) => ({
            id: nid,
            view: 'public'
        }))) : []
    }

}

