import 'vue-slick-carousel/dist/vue-slick-carousel.css'
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'

import VueSlickCarousel from 'vue-slick-carousel'

export function boot({Vue}) {
  Vue.component('slick-carousel', VueSlickCarousel);
}

export function request({Vue, router}) {


}
