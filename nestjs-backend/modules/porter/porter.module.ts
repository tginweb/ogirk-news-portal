import {Module} from '@nestjs/common';
import {HttpModule} from '@nestjs/axios';

import {PorterController, PorterService} from "./";

import {EntityModule} from "~modules/entity/entity.module";
import {PublisherModule} from "~modules/publisher/publisher.module";

@Module({
    imports: [
        EntityModule,
        PublisherModule,
        HttpModule
    ],
    controllers: [
        PorterController
    ],
    providers: [
        PorterService
    ],
    exports: [
        PorterService
    ],
})
export class PorterModule {
}
