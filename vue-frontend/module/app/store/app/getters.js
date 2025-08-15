import {findMenuPath} from './util'

export function defaultPostTypes(state) {
  return ['post', 'sm-quiz']
}

export function menuItemsFindPath(state) {

  return (url, menus = []) => {

    const rootItems = state.menuItems.filter(item => !menus.length || menus.indexOf(item.menu) > -1);

    return findMenuPath(rootItems, url);
  }
}

export function menuItemsSelect(state) {
  return (filter) => {
    return state.menuItems.filter(item => item.menu === filter.menu);
  }
}

export function menuItemsPrimary(state, getters) {
  return getters.menuItemsSelect({menu: 'primary'})
}

export function menuItemsPrimaryMobile(state, getters) {
  return getters.menuItemsSelect({menu: 'primary-mobile'})
}


export function menuItemsPath(state, getters) {
  return (url, menus) => {
    return getters.menuItemsFindPath(url, menus)
  }
}

export function menuItemsContext(state, getters) {
  return (url, menus) => {
    const path = getters.menuItemsPath(url, menus) || []

    let res = []

    if (path.length) {
      const lastItem = path.pop()
      const lastPrevItem = path.pop()

      if (lastItem.children && lastItem.children.length) {
        res = lastItem.children

      } else if (lastPrevItem) {
        res = [...lastPrevItem.children]
        //res.unshift(lastPrevItem)
      }
    }

    return res
  }
}

export function contextLoaded(state, getters, rootState) {
  return state.contextLoaded;
}



