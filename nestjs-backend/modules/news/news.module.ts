import {Module} from '@nestjs/common';
import {PostModel} from "~modules/entity/entity/post/post.model";
import {TermModel} from "~modules/entity/entity/term/term.model";

import {TypegooseModule} from "nestjs-typegoose";
import {
    NewsIssuePostModel,
    NewsIssueTermModel,
    NewsIssuePostResolvers,

    NewsPostPostModel,
    NewsPostPostResolvers,

    NewsCategoryTermModel,
    NewsTagTermModel,

    NewsHubPostModel,
    NewsHubPostResolvers,

    NewsAuthorTermModel,

    NewsAdPostModel,
    NewsAdController,

    NewsQuizPostModel,
    NewsConfPostModel
} from './';

@Module({

    imports: [
        TypegooseModule.forFeature([
            {
                typegooseClass: PostModel,
                discriminators: [
                    {
                        typegooseClass: NewsConfPostModel,
                        discriminatorId: 'sm-conference'
                    },
                    {
                        typegooseClass: NewsQuizPostModel,
                        discriminatorId: 'sm-quiz'
                    },
                    {
                        typegooseClass: NewsIssuePostModel,
                        discriminatorId: 'sm-issue-print'
                    },
                    {
                        typegooseClass: NewsHubPostModel,
                        discriminatorId: 'sm-hub-post'
                    },
                    {
                        typegooseClass: NewsAdPostModel,
                        discriminatorId: 'sm-ad-item'
                    },
                    {
                        typegooseClass: NewsPostPostModel,
                        discriminatorId: 'post'
                    },
                ]
            },
            {
                typegooseClass: TermModel,
                discriminators: [
                    {
                        typegooseClass: NewsIssueTermModel,
                        discriminatorId: 'sm-issue'
                    },
                    {
                        typegooseClass: NewsCategoryTermModel,
                        discriminatorId: 'category'
                    },
                    {
                        typegooseClass: NewsTagTermModel,
                        discriminatorId: 'post_tag'
                    },
                    {
                        typegooseClass: NewsAuthorTermModel,
                        discriminatorId: 'sm-author'
                    },
                ]
            },
        ]),
    ],
    exports: [],
    controllers: [
        NewsAdController
    ],
    providers: [
        NewsPostPostResolvers,
        NewsIssuePostResolvers,
        NewsHubPostResolvers
    ],
})
export class NewsModule {


}
