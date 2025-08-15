import {Args, Info, Query, Resolver} from '@nestjs/graphql';
import {UserRoles} from "~modules/user/decorators/user.roles.decorator";
import {TemplaterService} from "../core/templater.service";

@Resolver('Templater')
export class TemplaterAdminResolvers {

    constructor(
        private readonly templaterService: TemplaterService,
    ) {
    }

    @UserRoles('admin', 'operator')
    @Query('templaterAdmin_LoadRows')
    async loadRows(@Args() queryInput, @Info() info) {

    }

    @UserRoles('admin', 'operator')
    @Query('templaterAdmin_LoadRow')
    async loadRow(@Args('id') id) {
        return this._loadRow(id)
    }

    async _loadRow(id) {

    }
}
