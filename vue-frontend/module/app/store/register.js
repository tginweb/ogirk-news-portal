import mod_app from './app'
import mod_screen from './screen'

export default async ({modules}) => {

  modules.app = mod_app;
  modules.screen = mod_screen;

}
