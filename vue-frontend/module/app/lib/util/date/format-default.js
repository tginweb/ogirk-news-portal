const dayjs = require('dayjs')

export default function formatDefault(ts, format = 'datetime') {

  ts = parseInt(ts)

  switch (format) {
    case 'date':
      format = 'DD.MM.YYYY'
      break;
    case 'datetime':
      format = 'DD.MM.YYYY HH:mm'
      break;
    case 'time':
      format = 'HH:mm'
      break;
  }

  return dayjs(ts).format(format)
}
