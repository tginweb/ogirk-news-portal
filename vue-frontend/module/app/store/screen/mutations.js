
export function update(state, data) {

  const
    w = window.innerWidth,
    h = window.innerHeight

  if (h !== state.screen.height) {
    state.screen.height = h
  }

  if (w !== state.screen.width) {
    state.screen.width = w
  }

  let s = state.screen.sizes

  state.screen.gt.xs = w >= s.sm
  state.screen.gt.sm = w >= s.md
  state.screen.gt.md = w >= s.lg
  state.screen.gt.lg = w >= s.xl
  state.screen.lt.sm = w < s.sm
  state.screen.lt.md = w < s.md
  state.screen.lt.lg = w < s.lg
  state.screen.lt.xl = w < s.xl
  state.screen.xs = state.screen.lt.sm
  state.screen.sm = state.screen.gt.xs === true && state.screen.lt.md === true
  state.screen.md = state.screen.gt.sm === true && state.screen.lt.lg === true
  state.screen.lg = state.screen.gt.md === true && state.screen.lt.xl === true
  state.screen.xl = state.screen.gt.lg

  s = (state.screen.xs === true && 'xs') ||
    (state.screen.sm === true && 'sm') ||
    (state.screen.md === true && 'md') ||
    (state.screen.lg === true && 'lg') ||
    'xl'

  if (s !== state.screen.name) {
    state.screen.name = s
  }

}



export function setServerScreen(state, data) {

  state.serverScreen = data

}
