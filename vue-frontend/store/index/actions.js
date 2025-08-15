export * from '@common/core/store/dialogable/manager-actions'

function modulesRunHooks(data, context) {
  for (let key in data) {
    let [type, module, name] = key.split('__')
    if (type === 'dispatch') {
      context.dispatch(module + '/' + name, data[key]);
    } else {
      context.commit(module + '/' + name, data[key]);
    }
  }
}

export function fetchStateCommon(context, authorized) {

  return new Promise(async (resolve, reject) => {
    try {

      var {data} = await this.apollo.defaultClient.query({query: require('~module/app/graphql/fetchStateCommon.gql')})
      modulesRunHooks(data, context);
      resolve();
    } catch (e) {
      console.log(e)
      reject(e)
    }
  })
}

export function setPageData(context, data) {
  context.commit('setPageData', data);
}

export function setPageRouteData(context, data) {
  context.commit('setPageRouteData', data);
}
