import {Module} from '@nestjs/common';
import {TemplaterCoreModule} from './../core/templater.module';
import {TemplaterAdminResolvers} from './templater.resolvers';
import {CoreService} from "~modules/core/core.service";
import {CoreModule} from "~modules/core/core.module";

@Module({
    imports: [
        TemplaterCoreModule,
        CoreModule,
    ],
    providers: [
        TemplaterAdminResolvers
    ],
    exports: [],
})
export class TemplaterAdminModule {
    constructor(private coreService: CoreService) {
    }

    onModuleInit() {

        this.coreService.hooks.addFilter('menu', 'templater', (info) => {

            info['templater.list'] = {
                menu: 'admin',
                label: 'Templates',
                url: '/cab/admin/templates/',
                userRoles: ['admin', 'operator']
            }

            return info
        });

    }
}
