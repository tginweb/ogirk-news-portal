
export * from '@common/core/store/dialogable/manager-actions'
import {dom, scroll} from 'quasar'

const {getScrollTarget, setScrollPosition} = scroll

function modulesFillTerms(terms, context) {

  if (!terms) return;

  let termsByModule = {}

  terms.forEach((term) => {
    let [termModule, termTaxonomy] = term.taxonomy.split('.')

    if (!termsByModule[termModule])
      termsByModule[termModule] = []

    termsByModule[termModule].push(term)
  })

  for (let termModule in termsByModule) {
    context.commit(termModule + '/setTerms', termsByModule[termModule]);
  }
}
