const routes = [

    {
        path: '/',
        components: {
            default: require('~module/app/layout/main').default,
        },

        children: [
            {
                path: '',
                components: {
                    default: require('~module/app/routes/front.vue').default,
                    'headline-side': require('~module/app/layout/main/header/block/headline-front-feed-switcher').default,
                }
            },

            {
                path: '/group-official/',
                component: () => import('~module/app/routes/official'),
                meta: {seoBottomHide: true}
            },
            {
                path: '/group-official/structure',
                component: () => import('~module/app/routes/official/structure'),
                meta: {seoBottomHide: true}
            },

            {
                path: '/feed/all',
                components: {
                    default: () => import('~module/app/routes/feed-all.vue'),
                    'headline-side': require('~module/app/layout/main/header/block/headline-front-feed-switcher').default,
                }
            },
            {
                path: '/archive/:date?',
                components: {
                    default: () => import('~module/app/routes/archive.vue'),
                }
            },
            {
                path: '/calendar',
                components: {
                    default: () => import('~module/app/routes/archive-calendar.vue'),
                }
            },

            {path: '/feed/news', component: () => import('~module/app/routes/feed-news.vue')},
            {path: '/feed/news/stream', component: () => import('~module/app/routes/feed-news-stream.vue')},

            {
                path: '/page/:code(.*)',
                component: () => import('~module/app/routes/page.vue'),
                meta: {seoBottomHide: true}
            },

            {path: '/projects', component: () => import('~module/app/routes/projects.vue')},
            {path: '/topics', component: () => import('~module/app/routes/topics.vue')},

            {path: '/video', component: () => import('~module/app/routes/video.vue'), meta: {headlineHide: true}},
            {path: '/foto', component: () => import('~module/app/routes/foto.vue')},
            {path: '/quotes', component: () => import('~module/app/routes/quotes.vue')},

            {path: '/search', component: () => import('~module/app/routes/search.vue')},

            {
                path: '/category/:slug/:date?',
                component: () => import('~module/app/routes/category.vue'),
                meta: {headlineHide: true}
            },
            {
                path: '/issue/:slug?/:date?',
                component: () => import('~module/app/routes/issue.vue'),
                meta: {headlineHide: 'mobile'}
            },
            //{path: '/district-issue/:slug?/:date?', component: () => import('~module/app/routes/district-issues.vue'), meta: {headlineHide: 'mobile'}},
            //{path: '/district-issue-pub/:nid', component: () => import('~module/app/routes/district-issue-pub.vue'), meta: {headlineHide: 'mobile'}},

            {path: '/hub/:slug', component: () => import('~module/app/routes/hub.vue'), meta: {headlineHide: true}},
            {path: '/tag/:slug', component: () => import('~module/app/routes/tag.vue')},

            {path: '/tags/activity', component: () => import('~module/app/routes/tags-activity.vue')},

            {path: '/authors', component: () => import('~module/app/routes/authors.vue')},
            {path: '/author/:slug/:date?', component: () => import('~module/app/routes/author.vue')},

            {path: '/games', component: () => import('~module/app/routes/games.vue')},
            {path: '/quiz/:slug', component: () => import('~module/app/routes/quiz.vue'), meta: {headlineHide: true}},

            {path: '/confs', component: () => import('~module/app/routes/confs.vue')},
            {path: '/conf/:slug', component: () => import('~module/app/routes/conf.vue'), meta: {headlineHide: true}},

            {
                path: '/ohrana-truda/',
                component: () => import('~module/app/routes/ohrana-truda/index'),
                meta: {seoBottomHide: true}
            },

            {path: '/zakupki/', component: () => import('~module/app/routes/zakupki'), meta: {seoBottomHide: true}},

            {path: '/advert', component: () => import('~module/app/routes/advert.vue'), meta: {seoBottomHide: true}},
            {
                path: '/advert/site/banner',
                component: () => import('~module/app/routes/advert-site-banner.vue'),
                meta: {seoBottomHide: true}
            },
            {
                path: '/advert/site/teaser',
                component: () => import('~module/app/routes/advert-site-teaser.vue'),
                meta: {seoBottomHide: true}
            },
            {
                path: '/advert/issue',
                component: () => import('~module/app/routes/advert-issue.vue'),
                meta: {seoBottomHide: true}
            },

            {
                path: '/issue-print/:slug',
                component: () => import('~module/app/routes/issuepub.vue'),
                meta: {
                    mview: true
                }
            },
            {
                path: '/line',
                component: () => import('~module/app/routes/line.vue'),
                meta: {
                    headlineHide: 'mobile',
                    mview: true,
                    seoBottomHide: true
                }
            },

            {
                path: '/:year(\\d+)/:month(\\d+)/:day(\\d+)/:slug',
                component: () => import('~module/app/routes/article.vue'),
                meta: {
                    headlineHide: 'mobile',
                    mview: true,
                    seoBottomHide: true
                }
            },


        ]
    },


]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
    routes.push({
        path: '*',
        component: () => import('~module/app/routes/Error404.vue')
    })
}

export default routes
