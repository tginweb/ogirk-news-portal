import {createHooks} from '@wordpress/hooks';
import {Injectable} from "@nestjs/common";

@Injectable()
export class CoreService {

    public hooks;

    constructor() {
        this.hooks = createHooks();
    }

    getMenuItems() {
        return this.applyFiltersResultArray('menu');
    }

    getMenuItemsByUser(user) {
        let items = this.getMenuItems();
        return this.filterMenuItemsByUser(items, user)
    }

    filterMenuItemsByUser(items, user) {
        return items.filter((item) => {

            if (item.userRoles &&  (!user || !item.userRoles.find(itemRole => user.roles.indexOf(itemRole) > -1)))
                return true

            return true
        })
    }

    applyFiltersResultArray(name) {
        let resultById = this.hooks.applyFilters(name, {})

        return Object.keys(resultById).map(id => ({
            ...resultById[id],
            id: id
        }))
    }
}
