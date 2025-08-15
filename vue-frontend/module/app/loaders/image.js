import CImageStyler from './../lib/image-styler/cloudinry'

export function boot(ctx) {

  ctx.inject('$image', new CImageStyler(ctx))

}

export function request(ctx) {

 // console.log(ctx.$config)

}
