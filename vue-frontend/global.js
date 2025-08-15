import CConfig from '@common/core/lib/config'
import CRegistry from '@common/core/lib/registry'
import CContainer from '@common/core/lib/di/container'

const EventEmitter = require('events')

export function createConfig(emitter) {

  let configData

  if (process.env.SERVER) {

    if (process.env.NODE_ENV === 'development')

      configData = require('./../config/development/server')
    else
      configData = require('./../config/development/server')

  } else {

    if (process.env.NODE_ENV === 'development')
      configData = require('./../config/development/client')
    else
      configData = require('./../config/development/client')

  }

  return new CConfig(configData, emitter)
}

export function createEmitter() {
  return new EventEmitter()
}

export function createRegistry() {
  return new CRegistry()
}

export function createContainer() {
  return new CContainer();
}

export const emitter = createEmitter()
export const config = createConfig(emitter)
export const registry = createRegistry()
export const container = createContainer()
