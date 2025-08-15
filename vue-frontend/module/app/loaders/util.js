import * as util from '@common/core/lib/util'

export function boot({Vue, inject}) {
  inject('$util.base', util.base)
  inject('$util.date', util.date)
  inject('$util.html', util.html)
  inject('$util.dom', util.dom)
}

export function request({Vue}) {

}
