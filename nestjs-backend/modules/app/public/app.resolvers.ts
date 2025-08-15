import {Args, Info, Mutation, Query, Resolver, Subscription} from '@nestjs/graphql';
import {TermService} from "~modules/entity/entity/term/term.service";
import {PostService} from "~modules/entity/entity/post/post.service";
import {PublisherService} from "~modules/publisher/publisher.service";
import {CacheService} from "~modules/cache/cache.service";


@Resolver('AppPublic')
export class AppPublicResolvers {

    constructor(
        private readonly termService: TermService,
        private readonly postService: PostService,
        private readonly publisherService: PublisherService,
        private readonly cacheService: CacheService,
        // @Inject(CACHE_MANAGER) private readonly cache: cacheManager
    ) {
    }


    @Mutation()
    async anticorruptionFeedbackSend(@Args() args) {

        const response = {
            _result: {
                success: true
            },
            payload: null
        }

        return response
    }

    @Mutation()
    async questionSend() {

        return {
            _result: {
                status: true,
                t: 1
            }
        }
    }

    @Subscription('noteAdded', {
        filter: (payload, variables) => {
            return true
            //  return !variables.parentNid || payload.parentNid === variables.parentNid
        },
        resolve: value => value,
    })
    noteAdded() {

    }

    @Query()
    async app_public_GetMenuItems(@Args() args, @Info() info) {

        //  info.cacheControl.setCacheHint({ maxAge: 60 });

        //  console.log(this.cache)

        const catsRs = await this.termService.find({
            input: {
                filter: {
                    taxonomy: "category"
                },
                nav: {
                    limit: 400,
                    sortField: 'name',
                    sortAscending: true
                },
                cache: 600
            },
            pager: 'cursor'
        });

        const catsById = {}, catsBySlug = {}, catsByParent = {}

        const cats = catsRs.nodes

        cats.forEach(cat => {
            catsBySlug[cat.slug] = cat

            if (cat.parent) {
                if (!catsByParent[cat.parent])
                    catsByParent[cat.parent] = []
                catsByParent[cat.parent].push(cat)
            }
        })

        const result = [
            {
                menu: 'primary',
                entitySlug: 'social'
            },
            {
                menu: 'primary',
                entitySlug: 'politics'
            },
            {
                menu: 'primary',
                entitySlug: 'economic'
            },
            {
                menu: 'primary',
                entitySlug: 'territory',
                dropdown: {
                    childFetchEvent: 'click',
                    childsScroll: true
                }
            },
            {
                menu: 'primary',
                entitySlug: 'incident'
            },
            {
                menu: 'primary',
                entitySlug: 'blogs'
            },
            {
                menu: 'primary',
                title: 'Видео',
                url: '/video'
            },
            {
                menu: 'primary',
                title: 'Фото',
                url: '/foto'
            },
            {
                menu: 'primary',
                title: 'Проекты',
                url: '/projects'
            },
            {
                menu: 'primary',
                title: 'Сюжеты',
                url: '/topics'
            },
            {
                menu: 'primary',
                title: 'Вопрос-ответ',
                url: '/confs'
            },
            {
                menu: 'primary',
                title: 'Редакция',
                children: [
                    {
                        title: 'О газете',
                        url: '/page/info-about'
                    },
                    {
                        title: 'Выпуски газеты',
                        url: '/issue/og',
                        children: [
                            {
                                title: 'Областная газета',
                                url: '/issue/og',
                            },
                            {
                                title: 'Спецвыпуск областной',
                                url: '/issue/ogspec',
                            },
                            {
                                title: 'Панорама округа',
                                url: '/issue/panorama',
                            },
                            {
                                title: 'Православный вестник',
                                url: '/issue/vestnik',
                            },
                        ]
                    },
                    {
                        title: 'Районные издания',
                        url: '/district-issue/all',
                        children: [
                            {
                                title: 'Все издания',
                                url: '/district-issue/all',
                            },
                            {
                                title: 'Усть-Ордын',
                                url: '/district-issue/ust-ordyn-unjen',
                            },
                            {
                                title: 'Вестник',
                                url: '/district-issue/jehirit-bulagatskij-vestnik',
                            },
                        ]
                    },
                    {
                        title: 'Авторы',
                        url: '/authors'
                    },
                    {
                        title: 'Реклама',
                        url: '/advert',
                        children: [
                            {
                                title: 'Баннерная реклама',
                                url: '/advert/site/banner'
                            },
                            {
                                title: 'Тизерная реклама',
                                url: '/advert/site/teaser'
                            },
                            {
                                title: 'Реклама в газете',
                                url: '/page/advert/issue'
                            },
                            {
                                title: 'Услуги пресс-центра',
                                url: '/page/advert/press-center'
                            },
                            {
                                title: 'Изготовление рекламы',
                                url: '/page/advert/production'
                            },
                        ]
                    },
                ]
            },

        ];

        Array.prototype.push.apply(result, [
            {
                menu: 'primary-mobile',
                title: 'Новости',
                url: '/',
                children: [
                    {
                        title: 'Главная',
                        url: '/',
                    },
                    {
                        title: 'Общество',
                        url: '/category/social',
                    },
                    {
                        title: 'Происшествия',
                        url: '/category/incident',
                    },
                    {
                        title: 'Власть',
                        url: '/category/politics',
                    },
                    {
                        title: 'Экономика',
                        url: '/category/economic',
                    },
                    {
                        title: 'Территории',
                        url: '/category/territory',
                    },
                ]
            },
            {
                menu: 'primary-mobile',
                title: 'Видео',
                url: '/video',
            },
            {
                menu: 'primary-mobile',
                title: 'Фото',
                url: '/foto',
            },
            {
                menu: 'primary-mobile',
                title: 'Выпуски',
                url: '/issue/og',
                children: [
                    {
                        title: 'Областная газета',
                        url: '/issue/og',
                    },
                    {
                        title: 'Спецвыпуск областной',
                        url: '/issue/ogspec',
                    },
                    {
                        title: 'Панорама округа',
                        url: '/issue/panorama',
                    },
                    {
                        title: 'Православный вестник',
                        url: '/issue/vestnik',
                    },
                ]
            },
            {
                menu: 'primary-mobile',
                title: 'Проекты',
                url: '/projects',
                children: []
            },
            {
                menu: 'primary-mobile',
                title: 'Правовой портал',
                url: 'https://www.ogirk.ru/pravo/reestr',
            },
        ]);

        return result.map(item => {

            const res: any = {...item}

            let entity

            if (item.entitySlug) {
                entity = catsBySlug[item.entitySlug]
            }

            if (entity) {
                res.title = entity.name
                res.url = entity.url
                res.entityNid = entity.nid

                if (catsByParent[entity.nid] && catsByParent[entity.nid].length) {

                    if (!res.dropdown) res.dropdown = {}

                    if (entity.url === '/category/territory/') {
                        res.dropdown = {
                            is: 'nav-submenu-cat-ter',
                            block: true,
                            ...res.dropdown
                        }
                    } else {
                        res.dropdown = {
                            is: 'nav-submenu-cat',
                            block: true,
                            ...res.dropdown
                        }
                    }


                    res.children = catsByParent[entity.nid].map(child => ({
                        entityNid: child.nid,
                        title: child.name,
                        url: child.url
                    }))
                }
            }

            return res;
        })

    }
}
