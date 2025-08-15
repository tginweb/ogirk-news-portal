const dayjs = require('dayjs')
const isYesterday = require('dayjs/plugin/isYesterday')
const isToday = require('dayjs/plugin/isToday')
const utc = require('dayjs/plugin/utc') // dependent on utc plugin
const timezone = require('dayjs/plugin/timezone')
require('dayjs/locale/ru')

export function boot({Vue}) {
  dayjs.extend(utc)
  dayjs.extend(timezone)
  dayjs.extend(isYesterday)
  dayjs.extend(isToday)

  dayjs.tz.setDefault('GMT')

  dayjs.locale('ru')
}

export function request(ctx) {

}
