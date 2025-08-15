
export default class ComLightbox {

  constructor(el, props, context, vnode) {
    this.$el = el;
    this.props = props;
    this.onClickHandle = this.onClick.bind(this)
    this.vnode = vnode

    console.log()
    el.addEventListener('click', this.onClickHandle)
  }

  onClick(e) {
    e.preventDefault();

    this.vnode.$store.dispatch('dialogShow', ['app/lightbox', this.props])
  }

  destroy() {
    this.$el.removeEventListener('click', this.onClickHandle)
  }
}

