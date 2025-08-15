import Vue from 'vue';

export function setPageData(state, data) {
  Vue.set(state, 'pageData', {})
  Object.assign(state.pageData, data)
}

export function setPageRouteData(state, data) {
  Vue.set(state.pageRouteData, data.path, data.data)
}

