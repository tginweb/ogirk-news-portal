import VueBus from 'vue-bus';

export function boot({Vue, inject}) {

 // Vue.use(VueBus);

 // inject('$emitter', Vue.bus)

  if (typeof window !== 'undefined') {
    window.bus = Vue.bus
  }
}

export function request(ctx) {

}
