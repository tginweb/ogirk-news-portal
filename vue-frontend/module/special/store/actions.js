
export function setSpecialMode(context, value) {

  context.commit('setSpecialMode', value);

  Cookies.set('special_mode', value, {path: '/'})

  if (typeof window !== 'undefined')
    window.location.reload()
}

