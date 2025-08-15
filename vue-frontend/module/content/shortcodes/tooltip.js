
export default class ComTooltip {

  constructor(el, props) {
    this.$el = el;
    this.props = props;

    var elCaption = el.querySelector('.tt-caption');
    var elContent = el.querySelector('.tt-content');

    this.$el.style.border = '1px solid #ddd';
  }

  render() {

  }

  destroy() {

  }
}

