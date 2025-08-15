import Vue from 'vue'
import VueRouter from 'vue-router'
import {Cookies} from 'quasar'

Vue.use(VueRouter)

export default function (ctx) {

  const store = ctx.store

  let sitemode = 'normal'

  if (ctx.ssrContext) {

    const host = ctx.ssrContext.req.headers.host

    switch (host) {
      case 'special.og.loc':
        sitemode = 'special'
        break;
    }

    //store.commit('app/setSiteMode', sitemode)

  } else {
    //sitemode = window.__INITIAL_STATE__ && window.__INITIAL_STATE__.app.siteMode
  }

  let routes

  if (sitemode === 'special') {
    //routes = require('./routes-special').default
  } else {
    routes = require('./routes').default
  }

  const Router = new VueRouter({
    routes,
    duplicateNavigationPolicy: 'reload',
    mode: process.env.VUE_ROUTER_MODE,
    base: process.env.VUE_ROUTER_BASE
  })

  let firstResolve = true

  Router.beforeResolve(async (to, from, next) => {

    let viewmode, viewmodeRecord;

    to.matched.forEach((record) => {

      if (record.meta.mview) {
        viewmode = 'overlay'
        viewmodeRecord = record
      }

    })

    if (firstResolve) {
      firstResolve = false
      next()
      return
    }

    switch (viewmode) {
      case 'overlay1':

        if (!to.meta.overlay) {

          //to.query.overlay = 1;
          // to.meta.overlay = 1;

          next({
            hash: to.hash,
            path: to.path,
            name: to.name,
            params: to.params,
            query: to.query,
            meta: to.meta,
            // replace: replace
          });

          return;
        }

        break;
    }

    next()
  })

  Router.beforeEach(async (to, from, next) => {


    const cookies = process.env.SERVER
      ? Cookies.parseSSR(ctx.ssrContext)
      : Cookies

    if (!to.query.ads_demo && from.query.ads_demo) {

      to.query.ads_demo = 1;

      next({
        hash: to.hash,
        path: to.path,
        name: to.name,
        params: to.params,
        query: to.query,
        meta: to.meta,
        // replace: replace
      });
    }

    next()

  })


  // METRIKA

  if (!process.env.SERVER && (process.env.NODE_ENV == 'production')) {

    const moduleOptions = {
      id: 24807167,
      defer: true,
      clickmap: true,
      trackLinks: true,
      accurateTrackBounce: true,
      webvisor: true
    }

    const ymUrl = (moduleOptions.useCDN ? 'https://cdn.jsdelivr.net/npm/yandex-metrica-watch' : 'https://mc.yandex.ru/metrika') + '/tag.js';

    (function (m, e, t, r, i, k, a) {
      m[i] = m[i] || function () {
        (m[i].a = m[i].a || []).push(arguments)
      };
      m[i].l = 1 * new Date();
      k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })
    (window, document, "script", ymUrl, "ym");

    ym(moduleOptions.id, 'init', moduleOptions);

    let firstRun = true

    Router.afterEach((to, from) => {

      ym(moduleOptions.id, 'hit', to.fullPath, {referer: from.fullPath})

      if (!firstRun) {
        if (window._tmr) {
          if (window._tmr.pageView) {
            window._tmr.pageView({id: "3073117", url: to.fullPath})
          } else {
            window._tmr.push({id: "3073117", type: "pageView", url: to.fullPath})
          }
        }
      }

      firstRun = false
    })
  }


  return Router
}
