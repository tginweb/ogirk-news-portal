export default function () {
  return {

    serverScreen: null,
    
    screen: {
      width: 0,
      height: 0,

      name: 'xs',

      sizes: {
        sm: 600,
        md: 1024,
        lg: 1440,
        xl: 1920
      },

      lt: {
        sm: true,
        md: true,
        lg: true,
        xl: true
      },
      gt: {
        xs: false,
        sm: false,
        md: false,
        lg: false
      },
      xs: true,
      sm: false,
      md: false,
      lg: false,
      xl: false,
    },
  }
}
