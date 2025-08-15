import {
    FilterValueId,
    FilterValueSlug,
    FilterValueDate,
    FilterOperationSimple,
    FilterOperationNumeric,
    FilterOperationString,
    FilterCondition,
    FilterConditionId,
    FilterConditionDate,
    FilterOperationSet
} from '~lib/query/types'


export class FilterConditionTaxonomy extends FilterCondition {
    op: FilterOperationSet;
    taxonomy: string;
    by: string;
    value: FilterValueId | FilterValueSlug;
    values: FilterValueId[] | FilterValueSlug[];
}
