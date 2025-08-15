import {Args, Query, Resolver} from '@nestjs/graphql';
import {CoreService} from "~modules/core/core.service";
import {UserCurrent} from "~modules/user/user.decorator";
import {UserModel} from "~modules/user/model/user.model";

@Resolver('Core')
export class CoreResolvers {

    constructor(
        private coreService: CoreService
    ) {
    }

    @Query()
    async core_Menu(
        @Args() args,
        @UserCurrent() user: UserModel
    ) {
        return this.coreService.getMenuItemsByUser(user);
    }

}
