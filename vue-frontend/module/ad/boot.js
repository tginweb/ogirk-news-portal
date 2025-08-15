const loaders = [
  require('./loaders/components'),
]


export function boot(ctx) {

  loaders.forEach((loader) => {
    loader.boot(ctx);
  })

}

export async function request(ctx) {

  loaders.forEach((loader) => {
    loader.request(ctx);
  })

}

