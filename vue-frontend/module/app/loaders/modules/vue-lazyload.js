import VueLazyload from 'vue-lazyload'

export function boot({Vue}) {
  Vue.use(VueLazyload, {
    preLoad1: 1.3,
    error: 'statics/loading.svg',
    loading: 'statics/loading.svg',
    attempt: 1
  })
}

export function request(ctx) {

}

