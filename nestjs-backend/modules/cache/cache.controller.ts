import { Controller, Get, Post, Query, Body } from '@nestjs/common';

import {CacheService} from './cache.service';

@Controller('cache')
export class CacheController {
  constructor(
      private readonly cacheService: CacheService
  ) {}

  @Get('clear/temporary')
  async clearTemporary(): Promise<any> {
    await this.cacheService.clearTemporary()
    return {
      result: true
    }
  }

  @Get('clear/permanent')
  async clearPermanent(): Promise<any> {
    await this.cacheService.clearPermanent()
    return {
      result: true
    }
  }


}
