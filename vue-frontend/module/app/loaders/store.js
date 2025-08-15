export function boot({Vue}) {

}

export function request({Vue, router, store}) {
  if (typeof window !== 'undefined') {
    window.vstore = store
  }
}
