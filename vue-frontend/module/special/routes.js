const routes = [

  {
    path: '/',
    components: {
      default: () => import('~module/app/layout/main'),
    },

    children: [
      {
        path: '',
        components: {
          default: () => import('~pages/special/front.vue'),
          'headline-side': () => import('~module/app/layout/main/header/block/headline-front-feed-switcher'),
        }
      },
      {path: '/feed/all', component: () => import('~pages/special/feed-all.vue')},
      {path: '/feed/news', component: () => import('~pages/special/feed-news.vue')},

      {path: '/page/:code(.*)', component: () => import('~pages/page.vue')},

      {path: '/projects', component: () => import('~pages/special/projects.vue')},
      {path: '/topics', component: () => import('~pages/special/topics.vue')},

      {path: '/video', component: () => import('~pages/special/video.vue'), meta: {headlineHide: true}},
      {path: '/foto', component: () => import('~pages/special/foto.vue')},
      {path: '/quotes', component: () => import('~pages/quotes.vue')},

      {path: '/search', component: () => import('~pages/search.vue')},

      {path: '/category/:slug?/:date?', component: (a) => import('~pages/special/category.vue'), meta: {headlineHide: true}},
      {path: '/issue/:slug?/:date?', component: () => import('~pages/issue.vue')},
      {path: '/issue-print/:slug', component: () => import('~pages/issuepub.vue')},

      {path: '/hub/:slug', component: () => import('~pages/hub.vue'), meta: {headlineHide: true}},
      {path: '/tag/:slug', component: () => import('~pages/tag.vue')},

      {path: '/tags/activity', component: () => import('~pages/tags-activity.vue')},

      {path: '/authors', component: () => import('~pages/authors.vue')},
      {path: '/author/:slug', component: () => import('~pages/author.vue')},

      {
        path: '/:year(\\d+)/:month(\\d+)/:day(\\d+)/:slug',
        component: () => import('~pages/special/article.vue'),
        meta: {
          headlineDisable: 'mobile',
          mview: true
        }
      },

  //    {path: '/regions-map', component: () => import('~pages/regions-map.vue')},
      {path: '/games', component: () => import('~pages/games.vue')},
      {path: '/quiz/:slug', component: () => import('~pages/quiz.vue'), meta: {headlineHide: true}},

      {path: '/confs', component: () => import('~pages/confs.vue')},
      {path: '/conf/:slug', component: () => import('~pages/conf.vue'), meta: {headlineHide: true}},

    ]
  },

  {
    path: '/clean',
    components: {
      default: () => import('~module/app/layout/clean'),
    },

    children: [

      {path: '/confs-clean', component: () => import('~pages/confs.vue')},

    ]
  },

  {path: '/embed/post/image/:id', component: () => import('~pages/embed/post/image.vue')},


]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
