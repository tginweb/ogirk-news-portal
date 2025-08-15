import axios from 'axios'

let axiosApiNode
let axiosApiRest

export function boot({Vue, inject, $config}) {

  axiosApiNode = axios.create({
    baseURL: $config.get('API_NODE_URL'),
    validateStatus1: false
  })

  axiosApiRest = axios.create({
    baseURL: $config.get('API_WP_URL'),
    validateStatus1: false
  })

  inject('$apiNode', axiosApiNode)
  inject('$apiWp', axiosApiRest)
}

export function request(ctx) {

}
