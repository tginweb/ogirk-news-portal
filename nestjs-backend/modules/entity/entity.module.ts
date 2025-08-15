
import {APP_INTERCEPTOR} from "@nestjs/core/constants";
import {DataLoaderInterceptor} from "~lib/interceptors/dataloader";

import {Module} from '@nestjs/common';
import {ImagingModule} from "~modules/imaging/imaging.module";

import {TypegooseModule} from "nestjs-typegoose";

import {EntityController} from './entity.controller';
import {ElasticsearchModule} from '@nestjs/elasticsearch';

import {EntityService} from './entity.service';

import {
    FileController,
    FileLoader,
    FileModel,
    FileResolvers,
    FileService,
    ImageResolvers,
    PostLoader,
    PostModel,
    PostQueryBuilder,
    PostResolvers,
    PostService,
    TermLoader,
    TermModel,
    TermQueryBuilder,
    TermResolvers,
    TermService,
} from './entity';

import {
    EntityStatModel,
    EntityStatService,
    EntityStatResolvers,
    EntityStatController
} from './entity-stat';

import {EntityResolvers} from './entity.resolvers'
import {CacheModule} from "~modules/cache/cache.module";

@Module({

    imports: [
        CacheModule,
        ElasticsearchModule.register({
            node: 'http://localhost:9200',
        }),

        ImagingModule,
        TypegooseModule.forFeature([
            PostModel,
            TermModel,
            FileModel,
            EntityStatModel
        ]),
    ],
    exports: [
        EntityService,
        EntityStatService,
        PostService,
        TermService,
        FileService,
    ],
    controllers: [
        EntityController,
        EntityStatController,
        FileController
    ],
    providers: [

        EntityResolvers,
        EntityService,

        EntityStatService,
        EntityStatResolvers,

        PostService,
        PostQueryBuilder,
        PostResolvers,
        PostLoader,

        TermService,
        TermQueryBuilder,
        TermResolvers,
        TermLoader,

        FileService,
        FileResolvers,
        FileLoader,

        ImageResolvers,

        {
            provide: APP_INTERCEPTOR,
            useClass: DataLoaderInterceptor,
        },
    ],
})
export class EntityModule {
}
