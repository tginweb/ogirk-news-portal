import {Injectable} from '@nestjs/common';
import {InjectConfig} from "nestjs-config";

const cacheManager = require('cache-manager');
const redisStore = require('cache-manager-ioredis');

const store = {}
const crypto = require('crypto');

@Injectable()
export class CacheService {

    manager: any
    client: any
    maxTempTTL: 3600
    cachePresets: {}

    constructor(
        @InjectConfig() private readonly config,
    ) {

        this.manager = cacheManager.caching({
            store: redisStore,
            ttl: 600,
            ...config.get('app.REDIS')
        });

        this.client = this.manager.store.getClient()

        this.cachePresets = {
            'TEMP_SM': {
                ttl: 60
            },
            'TEMP_MD': {
                ttl: 300
            },
            'TEMP_LG': {
                ttl: 360
            },
            'TEMP_XL': {
                ttl: 600
            },
            'TEMP_XXL': {
                ttl: 1800
            },
            'PERMANENT': {}
        }
    }

    public getCachePresetOptions(name): object | null {
        return this.cachePresets[name]
    }

    public async get(name): Promise<any> {
        return store[name]
    }

    public async set(name, value): Promise<any> {
        store[name] = value
    }

    public async clearPattern(pattern): Promise<any> {

        const patterns = Array.isArray(pattern) ? pattern : [pattern]

        patterns.forEach((patternItem)=>{

            let stream = this.client.scanStream({ match: patternItem, count: 100 });
            let pipeline = this.client.pipeline()
            let localKeys = [];

            stream.on('data',  (resultKeys) => {

                console.log("Data Received", resultKeys);

                for (var i = 0; i < resultKeys.length; i++) {
                    localKeys.push(resultKeys[i]);
                    pipeline.del(resultKeys[i]);
                }

                if(localKeys.length > 100){
                    pipeline.exec(()=>{console.log("one batch delete complete")});
                    localKeys=[];
                    pipeline = this.client.pipeline();
                }
            });

            stream.on('end', function(){
                pipeline.exec(()=>{console.log("final batch delete complete")});
            });

            stream.on('error', function(err){
                console.log("error", err)
            })

        })

    }

    public async wrapQuery(ns, query, cb) {

        let cacheParams = query.cacheTime || query.cache,
            cacheEnable = false,
            cacheCid = null,
            cacheOptions: any = {}


        if (cacheParams) {

            cacheEnable = true

            cacheCid = [
                'query',
                ns,
                query.queryId || 'default',
                crypto.createHash('md5').update(JSON.stringify(query)).digest('hex')
            ].join(':')

            if (typeof cacheParams !== 'object') {
                if (typeof cacheParams === 'number')
                    cacheParams = {ttl: cacheParams}
                else
                    cacheParams = {preset: cacheParams}
            }

            cacheOptions = {
                ttl: cacheParams.ttl
            }

            if (cacheParams.preset) {
                const presetOptions = this.getCachePresetOptions(cacheParams.preset)
                if (presetOptions) {
                    cacheOptions = {...cacheOptions, ...presetOptions}
                } else {
                    cacheEnable = false
                }
            }
        }

        if (cacheEnable) {
            return this.manager.wrap(cacheCid, async function () {
                const res = await cb()
                res.nodes = res.nodes.map(node => node.toJSON())
                return res
            }, cacheOptions);
        } else {
            return cb()
        }
    }

    public async clearTemporary(): Promise<any> {
        await this.clearPattern(['query:*'])
        await this.clearPattern(['page:*'])
    }

    public async clearPermanent(): Promise<any> {
        await this.clearPattern(['query:*'])
        await this.clearPattern(['page:*'])
        await this.clearPattern(['dataloader:term:*'])
    }
}

