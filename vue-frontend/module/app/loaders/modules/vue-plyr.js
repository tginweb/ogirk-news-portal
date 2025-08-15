import VuePlyr from 'vue-plyr'


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
