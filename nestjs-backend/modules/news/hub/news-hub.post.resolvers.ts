import {Args, Info, Parent, ResolveField, Resolver} from '@nestjs/graphql';
import {Loader} from "~lib/interceptors/dataloader";
import {PostLoader} from "~modules/entity/entity/post/post.dataloader";
import {PostModel} from "~modules/entity/entity/post/post.model";

import * as DataLoader from "dataloader";

@Resolver('Post')
export class NewsHubPostResolvers {

    @ResolveField()
    async metaHub(
        @Args() args,
        @Parent() parent,
        @Info() info,
        @Loader(PostLoader.name) postLoader: DataLoader<any, PostModel>,
    ) {
        const data: any = {}

        data.banner_image = parent.meta.sm_hub_banner_image && postLoader.load({id: parseInt(parent.meta.sm_hub_banner_image), view: 'public'})

        data.banner_bg_color = parent.meta.sm_hub_banner_bg_color
        data.banner_bg_size = parent.meta.sm_hub_banner_bg_size
        data.banner_bg_position = parent.meta.sm_hub_banner_bg_position

        data.banner_title = parent.meta.sm_hub_banner_title
        data.banner_subtitle = parent.meta.sm_hub_banner_subtitle

        return new Promise(async (resolve, reject) => {
            if (data.banner_image) {
                data.banner_image = await data.banner_image
                if ( data.banner_image)
                    data.banner_image.src = data.banner_image.file.src
            }
            resolve(data)
        })
    }
}
