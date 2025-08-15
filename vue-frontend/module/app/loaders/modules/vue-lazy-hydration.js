import LazyHydrate from 'vue-lazy-hydration'

export function boot({Vue}) {
  Vue.component('LazyHydrate', LazyHydrate);
}

export function request(ctx) {

}
