import {Args, Info, Parent, ResolveField, Resolver} from '@nestjs/graphql';
import {Loader} from "~lib/interceptors/dataloader";
import {PostLoader} from "~modules/entity/entity/post/post.dataloader";
import {PostModel} from "~modules/entity/entity/post/post.model";

import * as DataLoader from "dataloader";

@Resolver('Post')
export class NewsPostPostResolvers {

    @ResolveField()
    async metaQuote(
        @Args() args,
        @Parent() parent,
        @Info() info,
        @Loader(PostLoader.name) postLoader: DataLoader<any, PostModel>,
    ) {
        const data: any = {}

        data.image = parent.meta.sm_quote_speaker_foto && postLoader.load({id: parseInt(parent.meta.sm_quote_speaker_foto), view: 'public'})
        data.text = parent.meta.sm_quote_text
        data.speaker_name = parent.meta.sm_quote_speaker_name
        data.speaker_job = parent.meta.sm_quote_speaker_job

        return new Promise(async (resolve, reject) => {
            if (data.image) {
                data.image = await data.image
                if ( data.image)
                    data.image.src = data.image.file.src
            }
            resolve(data)
        })
    }


}
