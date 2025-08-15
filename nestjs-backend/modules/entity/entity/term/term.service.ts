import {Injectable} from '@nestjs/common';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {TermModel} from "./term.model";
import {TermQueryBuilder} from "./term.query-builder";
import {loaderBatchViews} from "~lib/dataloader/util";
import * as DataLoader from "dataloader";
import {CacheService} from "~modules/cache/cache.service";
import md5 from "~lib/util/base/md5";
import contentFormat from "~lib/util/content/format";

const RedisDataLoader = require('redis-dataloader')

@Injectable()
export class TermService {

    dataloader: any

    constructor(
        private readonly termQueryBuilder: TermQueryBuilder,
        @InjectModel(TermModel) public readonly termModel: ReturnModelType<typeof TermModel> | any,
        private readonly cacheService: CacheService,
    ) {
        this.dataloader = new (RedisDataLoader({redis: this.cacheService.client}))(
            'dataloader:term',
            new DataLoader(this.batchLoad.bind(this), {cache: false}),
            {
                cache: false,
                expire: 60 * 60 * 12,
                cacheKeyFn: data => md5(data)
            }
        );
    }


    batchLoad(keys) {

        const res = loaderBatchViews(keys, async (view) => {

            const inputFilter: any = {}

            if (view.bundle) {
                inputFilter.slug__in = view.ids
                inputFilter.taxonomy = view.bundle
            } else {
                inputFilter.nid__in = view.ids
            }

           // console.log({inputFilter: inputFilter, inputNav: {limit: 1000},})

            const result = await this.find({
                inputFilter: inputFilter,
                inputNav: {limit: 1000},
            })

            return result.nodes.map(node => ({
                ...node.toObject(),
            })).reduce((res, item) => {
                res[view.bundle ? item.slug : item.nid] = item
                return res;
            }, {})
        })

        return res
    }

    async find<T>({
                      query = null,
                      queryFilter = null,
                      querySort = null,
                      input = {},
                      inputFilter = null,
                      inputNav = null,
                      view = 'public',
                      pager = false,
                      pagerOverridable = [{}, 'limit', 'sortField', 'sortAscending'],
                      pagerOptions = {maxLimit: 200},
                      populates = []
                  }: any): Promise<any> {

        return await this.cacheService.wrapQuery('term.find', input, async () => {

            query = query || this.termModel.find()

            if (queryFilter) query.find(queryFilter)
            if (querySort) query.sort(querySort)

            if (inputFilter) input.filter = inputFilter
            if (inputNav) input.nav = inputNav

            await this.termQueryBuilder.build(query, input)

            populates.forEach(populate => query.popupate(populate))

            query.withView(input.view || view)

            if (pager)
                query.withPager(pager, input.nav || {}, pagerOverridable, pagerOptions)

            return this.termQueryBuilder.process(await query.execute(), input, query)
        })

    }

    async findOne<T>({
                         query = null,
                         input = {},
                         view = 'public',
                         populates = []
                     }: any): Promise<any> {
        query = query || this.termModel.findOne()
        await this.termQueryBuilder.build(query, input)
        populates.forEach(populate => query.popupate(populate))
        query.withView(view || input.view)
        query.findOne()
        return query;
    }

    async loadOne(by, val, tax = null) {
        return this.dataloader.load({
            id: val,
            bundle: tax,
            view: 'public'
        })
    }

    async loadMultiple(by, values, tax = null) {
        return this.dataloader.loadMany((Array.isArray(values) ? values : [values]).map((val) => {
            return {
                id: val,
                bundle: tax,
                view: 'public'
            }
        }))
    }

    async resolveSlugToNid<T>(slug, tax): Promise<number | string | [number | string]> {
        let doc = await this.termModel.findOne({slug: slug, taxonomy: tax})
        return doc ? doc.nid : null
    }

    async resolveSlugsToNids<T>(slugs, tax): Promise<number | string | [number | string]> {
        let docs = await this.termModel.find({slug: {$in: slugs}, taxonomy: tax})
        return docs.map(doc => doc.nid)
    }

    async getTermChildrenNids<T>(termId, tax): Promise<any> {
        const termIds = Array.isArray(termId) ? termId : [termId]
        let docs = await this.termModel.find({parent: {$in: termIds}, taxonomy: tax})
        return docs.map(doc => doc.nid)
    }


}
