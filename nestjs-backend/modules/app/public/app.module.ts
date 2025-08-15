import {Module} from '@nestjs/common';

import {EntityModule} from "~modules/entity/entity.module";
import {PublisherModule} from "~modules/publisher/publisher.module";
import {CacheModule} from "~modules/cache/cache.module";
import {PorterModule} from "~modules/porter/porter.module";

import {AppService} from "./app.service";
import {AppPublicResolvers} from "./";

@Module({
    imports: [
        EntityModule,
        PublisherModule,
        CacheModule,
        PorterModule,
    ],
    providers: [
        AppPublicResolvers,
        AppService
    ],
})
export class AppPublicModule {

}
