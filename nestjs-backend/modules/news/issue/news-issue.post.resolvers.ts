import {Args, Info, Parent, ResolveField, Resolver} from '@nestjs/graphql';
import {Loader} from "~lib/interceptors/dataloader";
import {PostLoader} from "~modules/entity/entity/post/post.dataloader";
import {PostModel} from "~modules/entity/entity/post/post.model";

import * as DataLoader from "dataloader";

@Resolver('Post')
export class NewsIssuePostResolvers {

    @ResolveField()
    async metaIssue(
        @Args() args,
        @Parent() parent,
        @Info() info,
        @Loader(PostLoader.name) postLoader: DataLoader<any, PostModel>,
    ) {
        const data: any = {}

        data.file = parent.meta.sm_file_official && postLoader.load({id: parseInt(parent.meta.sm_file_official), view: 'public'})
        data.post_num_year = parent.meta.sm_post_num_year
        data.post_num_all = parent.meta.sm_post_num_all

        return new Promise(async (resolve, reject) => {
            if (data.file) {
                data.file = await data.file
                if ( data.file)
                    data.file.src = data.file.file.src
            }
            resolve(data)
        })
    }
}
