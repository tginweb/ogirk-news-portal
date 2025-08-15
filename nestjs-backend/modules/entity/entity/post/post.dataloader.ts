import * as DataLoader from 'dataloader'
import { Injectable } from "@nestjs/common";
import { NestDataLoader } from "~lib/interceptors/dataloader";
import { loaderCacheKeyJsonable, loaderBatchViews } from "~lib/dataloader/util";

import {
    PostModel,
    PostService
} from "./index"

@Injectable()
export class PostLoader implements NestDataLoader<string, PostModel> {
    constructor(
        private readonly postService: PostService
    ) {

    }

    generateDataLoader(): DataLoader<string, PostModel> {

        return new DataLoader<any, any>(async (keys: any) => {

            return loaderBatchViews(keys, async (view) => {

                const result = await this.postService.find({
                    inputFilter: {'nid__in': view.ids},
                    inputNav: {limit: 1000},
                })

                return result.nodes.reduce((res, item) => {
                    res[item.nid] = item
                    return res;
                }, {})
            })
        }, {
            cacheKeyFn: loaderCacheKeyJsonable
        });
    }
}
