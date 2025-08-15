import Vue from 'vue'
import Vuex from 'vuex'

import modIndex from './index/index.js'

const modules = {}

require('~module/app/store/register').default({modules})
require('~module/ad/store/register').default({modules})

Vue.use(Vuex)

export default function (/* { ssrContext } */) {

  const Store = new Vuex.Store({
    strict: process.env.DEV,
    modules: modules,
    ...modIndex,
  })

  return Store
}
