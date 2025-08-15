import {Module} from '@nestjs/common';
import {CoreService} from './core.service';
import {CoreResolvers} from "./core.resolvers";

@Module({
    providers: [
        CoreService,
        CoreResolvers
    ],
    exports: [
        CoreService,
    ],
})
export class CoreModule {
}
