import {Injectable} from '@nestjs/common';
import {InjectModel} from "nestjs-typegoose";
import {TermModel} from "~modules/entity/entity/term/term.model";
import {ReturnModelType} from "@typegoose/typegoose";
import {QueryBuilderService} from "~lib/query/query-builder-service";

@Injectable()
export class TermQueryBuilder extends QueryBuilderService {

    public schema = {
        'nid': {field: 'nid', op: 'equals'},
        'taxonomy': {field: 'taxonomy', op: 'equals'},
        'taxonomies': {field: 'taxonomy', op: 'in'},
        'slug': {field: 'slug', op: 'equals'},
        'format': {field: 'format', op: 'equals'},
        'name': {field: 'name', op: 'equals'},
        'parent': {field: 'parent', op: 'equals'},
        'authorList': {field: 'meta.sm_author_list', op: 'exists'},
    }

    constructor(
        @InjectModel(TermModel) public readonly model: ReturnModelType<typeof TermModel>,
    ) {
        super();
    }

    process(results, input, query) {

        if (input.nav.sortFilter) {

            let filterKey = input.nav.sortFilter,
                filterValue = input.filter[input.nav.sortFilter]

            let [filterField, filterOp] = filterKey.split('__')
            let filterInfo = this.schema[filterField];

            if (filterInfo && filterValue !== null && filterValue !== '') {
                results.nodes = filterValue.map((fieldVal)=>{
                    return results.nodes.find(node => node[filterInfo.field] === fieldVal)
                })
            }
        }

        return results
    }
}
