export function boot({Vue}) {
  Vue.filter('price', function (value, space) {
    if (!value) return ''
    return value.toString() + space + '₽'
  })
}

export function request({Vue, router}) {

}
