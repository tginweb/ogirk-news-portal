const loaders = [
  require('./loaders/config'),
  require('./loaders/store'),
  require('./loaders/util'),
  require('./loaders/filters'),
  require('./loaders/modules/axios'),
  require('./loaders/modules/vue-plyr-ssr'),
  require('./loaders/modules/dayjs'),
  require('./loaders/modules/vue-lazyload'),
  require('./loaders/modules/vue-lazy-hydration'),
  require('./loaders/components'),
  require('./loaders/icons'),
  require('./loaders/image'),
]



if (!process.env.SERVER) {
  Array.prototype.push.apply(loaders, [
    require('./loaders/modules/vue-slick-carousel'),
    require('./loaders/modules/vue-social-sharing'),
    require('./loaders/modules/vue2-google-maps'),
    require('./loaders/modules/vue-hc-sticky'),
    require('./loaders/modules/vue-scroll-behavior'),
    require('./loaders/modules/v-video-embed'),
    require('./loaders/modules/vue-marquee-text-component'),
  ]);
}

export function boot(ctx) {

  loaders.forEach((loader) => {
    loader.boot(ctx);
  })

}

export async function request(ctx) {

  loaders.forEach((loader) => {
    loader.request(ctx);
  })

}

