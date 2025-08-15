import Vue from 'vue';
import {print} from "graphql";
export * from '@common/core/store/dialogable/mutations'

export function updatePage(state, data) {
  Object.assign(state.page, data)
}

export function setContextLoaded(state) {
  state.contextLoaded = true
}

export function setOption(state, [name, value]) {
  state.options[name] = value
}

export function setMenuItems(state, data) {
  state.menuItems = data
}

export function setMenus(state, data) {
  state.menuItems = data
}

export function setSiteMode(state, data) {
  state.siteMode = data
}


export function setCacheFragmentData(state, [cid, name, data]) {

  if (!state.cacheFragmentData[cid])
    Vue.set(state.cacheFragmentData, cid, {})

  state.cacheFragmentData[cid][name] = data

}


export function setCacheFragmentQuery(state, [cid, query, variables, result]) {

  if (this.ssrContext && this.ssrContext.req) {

    if (!this.ssrContext.req.fragments) {
      this.ssrContext.req.fragments = {}
    }

    const fragments = this.ssrContext.req.fragments;

    if (!fragments[cid])
      fragments[cid] = []

    const queryString = print(query)

    fragments[cid].push({
      query: queryString,
      variables,
      result
    })

  }

  if (!state.cacheFragmentQueries[cid])
    Vue.set(state.cacheFragmentQueries, cid, [])

  state.cacheFragmentQueries[cid] = [...state.cacheFragmentQueries[cid], {
    query,
    variables,
    result
  }]

}
