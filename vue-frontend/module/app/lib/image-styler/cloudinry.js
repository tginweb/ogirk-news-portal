export default class {

  constructor(ctx) {
    this.ctx = ctx

    this.loadParams();
    ctx.$config.on('updated', () => {
      this.loadParams();
    })
  }

  loadParams() {
    this.params = this.ctx.$config.get('IMAGE.STYLER', {})
    this.params.SITE_URL = this.ctx.$config.get('SITE_URL')
  }

  resolveUrl(url, width, height, crop) {

    const resWidth = width || 500

    let res = url.charAt(0) === '/' ? this.params.SITE_URL + url : url

    if (width || height) {
      if (crop) {
        res = 'https://res.cloudinary.com/' + this.params.CLOUD_ID + '/image/fetch/f_auto,c_limit,w_' + resWidth + '/' + res;
      } else {
        res = 'https://res.cloudinary.com/' + this.params.CLOUD_ID + '/image/fetch/f_auto,c_limit,w_' + resWidth + '/' + res;
      }
    }

    return res;
  }
}

