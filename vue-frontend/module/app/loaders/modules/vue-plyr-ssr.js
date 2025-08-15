const VuePlyr = require('~nm/vue-plyr/vue-plyr.ssr.js').default

require('plyr/dist/plyr.css')

export function boot({Vue}) {
  Vue.use(VuePlyr, {
    plyr: {
      fullscreen: {enabled: true}
    },
    emit: ['ended']
  })

}

export function request(ctx) {

}
