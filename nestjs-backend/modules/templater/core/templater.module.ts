import {Module} from '@nestjs/common';
import {TemplaterService} from './templater.service';
import {CoreService} from "~modules/core/core.service";
import {CoreModule} from "~modules/core/core.module";
import {TypegooseModule} from "nestjs-typegoose";
import {TemplateModel} from "./template.model";

@Module({
    imports: [
        CoreModule,

        TypegooseModule.forFeature([
            TemplateModel,
        ]),
    ],
    providers: [
        TemplaterService,
    ],
    exports: [
        TemplaterService,
    ],
})
export class TemplaterCoreModule {
    constructor(private coreService: CoreService) {
    }

    onModuleInit() {

    }
}
