import { Controller, Get, Post, Body } from '@nestjs/common';
import {I18n, I18nContext} from '~modules/i18n'
import {I18nService} from "~modules/i18n";

let tm = Date.now();

@Controller('entity')
export class EntityController {
  constructor(
      private readonly i18nService: I18nService
  ) {}

  @Get('info')
  async findAll(@I18n() i18n: I18nContext): Promise<any> {

    let byLang = await this.i18nService.getTranslates();

    let res = {}

    for (let key in byLang.ru) {
      res[key] = {
        ru: byLang.ru[key],
        en: byLang.en[key],
      }
    }

    return tm + '-----'
  }

}
