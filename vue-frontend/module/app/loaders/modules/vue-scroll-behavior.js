import vueScrollBehavior from '@common/packages/vue-scroll-behavior/vue-scroll-behavior'

export function boot({Vue}) {

}

export function request({Vue, router, store}) {
  Vue.use(vueScrollBehavior, {
    router: router,    // The router instance
    bus: Vue.bus,
    el: '#q-app-fake',        // Custom element
    maxLength: 100,    // Saved history List max length
    delay: 1200          // Delay by a number of milliseconds
  })
}
