import {Injectable} from '@nestjs/common';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {PostModel} from "./post.model";

import {PostQueryBuilder} from './post.query-builder'
import {ElasticsearchService} from '@nestjs/elasticsearch';
import {CacheService} from "~modules/cache/cache.service";

const esb = require('elastic-builder'); // the builder

@Injectable()
export class PostService {

    constructor(
        private readonly postQueryBuilder: PostQueryBuilder,
        @InjectModel(PostModel) public readonly postModel: ReturnModelType<typeof PostModel>,
        private readonly elasticsearchService: ElasticsearchService,
        private readonly cacheService: CacheService,
    ) {
    }

    async searchElastic(query: any): Promise<any> {

        const qb = esb.requestBodySearch()

        qb.source(["ID"])


        query.nav = {
            limit: 3,
            page: 0,
            sortField: 'post_date',
            sortAscending: false,
            ...(query.nav || {})
        }

        qb.sort(esb.sort(query.nav.sortField, query.nav.sortAscending ? 'asc' : 'desc'))

        qb.size(query.nav.limit)

        if (query.nav.page) {
            qb.from(query.nav.limit * (query.nav.page - 1))
        }

        if (query.filter) {

            if (query.filter.phrase) {

                const phraseFields = [
                    "post_title^3",
                    "post_excerpt^1",
                    "post_content^1",
                    "terms.category.name^1",
                    "terms.post_tag.name^1"
                ];

                qb.query(
                    esb.functionScoreQuery()
                        .query(
                            esb.boolQuery()
                                .must(
                                    esb.boolQuery().should([
                                        esb.multiMatchQuery(phraseFields, query.filter.phrase)
                                            .type('phrase')
                                            .boost(4),

                                        esb.multiMatchQuery(phraseFields, query.filter.phrase)
                                            .operator('and')
                                            .fuzziness(0)
                                            .boost(2)
                                    ])
                                )
                                .filter([
                                    esb.matchQuery('post_type.raw', 'post')
                                ])
                        )
                        .boostMode('sum')
                        .scoreMode('avg')
                )

                qb.highlight(
                    esb.highlight()
                        .numberOfFragments(2)
                        .fragmentSize(90)
                        .fields(['post_title', 'post_content'])
                        .preTags('<em>', '_all')
                        .postTags('</em>', '_all')
                        .fragmentSize(255, 'post_title')
                )
            }
        }

        return {}
    }

    async findElastic<T>({
                             input = {},
                             view = 'public',
                             pager = false,
                             pagerOverridable = [{}, 'limit', 'sortField', 'sortAscending'],
                             pagerOptions = {maxLimit: 500},
                             populates = []
                         }: any): Promise<any> {

        const highlights = []

        const esResult = await this.searchElastic(input);

        const esResultByNid = esResult.body.hits.hits.reduce((res, item) => {
            res[item._source.ID] = item
            return res;
        }, {})

        const query = this.postModel.find({nid: {$in: Object.keys(esResultByNid).map(nid => parseInt(nid))}})

        query['withView'](input.view || view)

        let docs = await query.exec();

        docs.forEach((doc) => {

            const hit = esResultByNid[doc.nid.toString()]

            highlights.push({
                _id: doc._id,
                nid: doc.nid,
                fragments: hit.highlight
            })
        })

        const result = {
            nodes: docs,
            pageInfo: {
                total: esResult.body.hits.total.value,
                highlights: highlights
            }
        }

        return result
    }

    async findOne<T>({
                         query = null,
                         input = {},
                         view = 'public',
                         populates = []
                     }: any): Promise<any> {

        query = query || this.postModel.findOne()
        await this.postQueryBuilder.build(query, input)
        populates.forEach(populate => query.popupate(populate))
        query.withView(input.view || view)
        query.findOne()

        return query;
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


        const result = await this.cacheService.wrapQuery('post.find', input, async () => {

            query = query || this.postModel.find()

            if (queryFilter) query.find(queryFilter)
            if (querySort) query.sort(querySort)

            if (inputFilter) input.filter = inputFilter
            if (inputNav) input.nav = inputNav

            await this.postQueryBuilder.build(query, input)

            populates.forEach(populate => query.popupate(populate))

            query.withView(input.view || view)

            if (pager)
                query.withPager(pager, input.nav || {}, pagerOverridable, pagerOptions)
            else {
                input.nav = {
                    limit: 20,
                    sortField: 'created',
                    sortAscending: false,
                    ...input.nav
                }
                query.limit(input.nav.limit)
                query.sort({[input.nav.sortField]: input.nav.sortAscending ? 1 : -1})
            }

            let result = await query.execute();


            if (input.alterable) {
                result = await this.filterQueryResult(result, input, populates)
            }

            result.nodes = result.nodes.filter((post) => post.nid !== 453143)

            return result
        })

        return result;
    }

    async filterQueryResult(result, {queryId, view}, populates) {

        const utime = Date.now() / 1000

        const alterQuery = this.postModel.find({
            'meta.sm_query_advert': {
                $elemMatch: {
                    'query_id': queryId,
                    'date_start': {$lt: utime},
                    'date_end': {$gt: utime},
                }
            }
        })

        alterQuery['withView'](view);

        populates.forEach(populate => alterQuery.populate(populate))

        let alterPosts = await alterQuery;

        if (alterPosts.length) {

            const overrides = alterPosts.map((post) => {
                return {
                    post: post,
                    alter: post.meta['sm_query_advert'].find(item => item.query_id === queryId)
                }
            }).sort((a, b) => a.alter.position - b.alter.position)


            overrides.forEach((data) => {

                const existPost = result.nodes.find((post) => post.nid === data.post.nid)

                if (existPost) {
                    const existPostIndex = result.nodes.indexOf(existPost)
                    result.nodes.splice(existPostIndex, 1)
                }

                let position = data.alter.position - 1

                result.nodes.splice(position, 0, data.post)
            })
        }

        return result
    }

    async findCalendar<T>({
                              input = {},
                          }: any): Promise<any> {


        let stages = [], createdFrom = {}, createdTo = {}

        if (input.filter.year && input.filter.month && input.filter.day) {
            createdFrom = new Date(input.filter.year, input.filter.month - 1, input.filter.day, 0, 0)
            createdTo = new Date(input.filter.year, input.filter.month - 1, input.filter.day, 23, 59)
        } else if (input.filter.year && input.filter.month) {
            createdFrom = new Date(input.filter.year, input.filter.month - 1, 1)
            createdTo = new Date(input.filter.year, input.filter.month, 0)
        } else if (input.filter.year) {
            createdFrom = new Date(input.filter.year, 0, 1)
            createdTo = new Date(input.filter.year, 12, 0)
        }

        const query = this.postModel.find()

        await this.postQueryBuilder.build(query, input)

        stages.push({
            $project: {
                _id: 1,
                created: 1,
                type: 1,
                taxonomy: 1,
                year: {$year: '$created'},
                month: {$month: '$created'},
            }
        })

        stages.push({
            $match: {
                created: {$gte: createdFrom, $lte: createdTo},
                ...query.getQuery()
            }
        })

        stages.push({
            $group: {
                _id: {
                    month: {$month: "$created"},
                    day: {$dayOfMonth: "$created"},
                    year: {$year: "$created"},
                },
                count: {$sum: 1}
            }
        })

        return this.postModel.aggregate(stages)
    }

    async findQuery<T>({
                           query = null,
                           input = {},
                       }: any): Promise<any> {
        query = query || this.postModel.find()
        await this.postQueryBuilder.build(query, input)
        return query
    }
}
