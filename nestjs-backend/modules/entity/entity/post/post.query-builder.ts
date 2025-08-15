import {Injectable} from '@nestjs/common';
import {TermService} from "~modules/entity/entity/term/term.service";
import {InjectModel} from "nestjs-typegoose";
import {PostModel} from "~modules/entity/entity/post/post.model";
import {ReturnModelType} from "@typegoose/typegoose";
import {QueryBuilderService} from "~lib/query/query-builder-service";

@Injectable()
export class PostQueryBuilder extends QueryBuilderService {

    public schema = {
        '_id': {field: '_id', op: 'equals'},
        'nid': {field: 'nid', op: 'equals'},
        'type': {field: 'type', op: 'equals'},
        'parentNid': {field: 'parentNid', op: 'equals'},
        'slug': {field: 'slug', op: 'equals'},
        'codePath': {field: 'codePath', op: 'equals'},
        'format': {field: 'format', op: 'equals'},
        'excludeFormat': {field: 'format', op: 'nin'},
        'title': {field: 'title', op: 'equals'},
        'haveImage': {field: 'thumb.post_id', op: 'exists'},
        'issue_print': {field: 'meta.sm_issue_print', op: 'equals'},
        'search': {field: 'title', op: 'contains'},
        'excludeNids': {field: 'nid', op: 'nin'},
        'notArchive': (query, value) => {
            query.where('meta.archive', {$ne: true})
        },
        'maxAge': (query, value) => {
            let createdFrom
            createdFrom = new Date();
            createdFrom.setDate(createdFrom.getDate() - value);
            query.where('created', {$gte: createdFrom})
        },
        'date': (query, _value) => {

            let createdFrom, createdTo, value: any = {}

            if (typeof _value === 'string') {
                const parts = _value.split('-')
                value.year = Math.abs(parseInt(parts[0]))
                value.month = Math.abs(parseInt(parts[1]))
                value.day = Math.abs(parseInt(parts[2]))

                if (value.year && (value.year > 2030)) value.year = null;
                if (value.month && (value.month > 12)) value.month = null;
                if (value.day && (value.day > 31)) value.day = null;
            } else {
                value = _value
            }

            if (value.year && value.month && value.day) {
                createdFrom = new Date(value.year, value.month - 1, value.day, 0, 0)
                createdTo = new Date(value.year, value.month - 1, value.day, 23, 59)
            } else if (value.year && value.month) {
                createdFrom = new Date(value.year, value.month - 1, 1)
                createdTo = new Date(value.year, value.month - 1, 31)
            } else if (value.year) {
                createdFrom = new Date(value.year, 0, 1)
                createdTo = new Date(value.year, 11, 31)
            }

            if (createdFrom && createdTo)
                query.where('created', {$gte: createdFrom, $lte: createdTo})
        },
        'query': (query, _value) => {

            // elastic.query(query);

            const nids = []

            query.where('nid', {$in: nids})
        },
        'terms': (query, _value) => {

            // elastic.query(query);

            const conds = []

            for (let tax in _value) {
                conds.push({['taxonomy.' + tax]: {$in: _value[tax]}})
            }

            query.or(conds)

        },
    }

    constructor(
        @InjectModel(PostModel) public readonly model: ReturnModelType<typeof PostModel>,
        private readonly termService: TermService
    ) {
        super();
    }

    async filterFetchMatches(docQuery, filters) {

        let result = [];

        if (filters.tax) {

            for (let filter of filters.tax) {

                let nids = []

                if (filter.id || filter.ids) {

                    nids = filter.id ? [filter.id] : filter.ids

                    if (filter.withChildren) {
                        (await this.termService.loadMultiple('id', nids)).filter(item => !!item).forEach((term) => {
                            if (term.children && term.children.length)
                                Array.prototype.push.apply(nids, term.children)
                        })
                    }

                } else if (filter.slug || filter.slugs) {

                    const terms = await this.termService.loadMultiple('slug', filter.slug || filter.slugs, filter.taxonomy)

                    terms.filter(item => !!item).forEach(term => {
                        nids.push(term.nid)
                        if (filter.withChildren)
                            if (term.children && term.children.length)
                                Array.prototype.push.apply(nids, term.children)
                    })
                }

                result.push({
                    field: 'taxonomy.' + filter.taxonomy,
                    op: filter.op || 'in',
                    value: nids,
                })
            }
        }

        return result;
    }

}
