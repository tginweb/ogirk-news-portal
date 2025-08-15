import {Module} from '@nestjs/common';

import {ImagingController} from "./imaging.controller";
import {ImagingService} from "./imaging.service";

@Module({

    imports: [

    ],
    controllers: [
        ImagingController
    ],
    providers: [
        ImagingService,
    ],
    exports: [
        ImagingService,
    ],
})
export class ImagingModule {

}
