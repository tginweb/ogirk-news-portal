import * as DataLoader from 'dataloader'
import {Injectable} from "@nestjs/common";
import {NestDataLoader} from "~lib/interceptors/dataloader";
import {loaderBatchViews} from "~lib/dataloader/util";
import {TermModel, TermService} from "./index"

const RedisDataLoader = require('redis-dataloader')
const redisClient = require('redis').createClient();

@Injectable()
export class TermLoader implements NestDataLoader<string, TermModel> {

    redisLoaderConstructor: any

    constructor(
        private readonly termService: TermService
    ) {
        this.redisLoaderConstructor = RedisDataLoader({redis: redisClient})
    }

    batchLoad(keys) {

        return loaderBatchViews(keys, async (view) => {

            const result = await this.termService.find({
                inputFilter: {'nid__in': view.ids},
                inputNav: {limit: 1000},
            })

            return result.nodes.reduce((res, item) => {
                res[item.nid] = item
                return res;
            }, {})
        })
    }

    generateDataLoader(): DataLoader<string, TermModel> {

        return new this.redisLoaderConstructor(
            // set a prefix for the keys stored in redis. This way you can avoid key
            // collisions for different data-sets in your redis instance.
            'og',
            // create a regular dataloader. This should always be set with caching disabled.
            new DataLoader(this.batchLoad.bind(this), {cache: false}),
            // The options here are the same as the regular dataloader options, with
            // the additional option "expire"
            {
                // caching here is a local in memory cache. Caching is always done
                // to redis.
                cache: true,
                // if set redis keys will be set to expire after this many seconds
                // this may be useful as a fallback for a redis cache.
                expire: 60,
                // can include a custom serialization and deserialization for
                // storage in redis.
                //   serialize: date => date.getTime(),
                //   deserialize: timestamp => new Date(timestamp)
            }
        );

    }
}
