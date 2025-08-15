import * as coms from './shortcodes'

export function boot(ctx) {

  ctx.$registry.addHook('content/shortcodes', (data) => {
    data['lightbox'] = {
      cls: require('./shortcodes/lightbox').default
    }

    data['tooltip'] = {
      cls: require('./shortcodes/tooltip').default
    }
  })

  ctx.$registry.addHook('content/handlers', (data) => {


    data['lightbox'] = {
      selector: 'a[href$=".jpeg"],a[href$=".jpg"],a[href$=".png"]',
      event: 'click',
      callback: (el, context, vnode) => {

        const elFigure = el.closest('figure');

        const comParams = {
          url: el.getAttribute('href')
        }

        if (elFigure) {

          const elFigureCaption = elFigure.querySelector('figcaption')

          if (elFigureCaption) {
            comParams.caption = elFigureCaption.innerHTML
          }
        }

        vnode.$store.dispatch('dialogShow', ['app/lightbox', comParams])
      }
    }

  })


}

export async function request(ctx) {


}
