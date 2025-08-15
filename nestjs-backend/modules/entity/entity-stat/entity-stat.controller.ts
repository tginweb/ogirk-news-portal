import {Controller, Get, Query} from '@nestjs/common';
import {EntityStatService} from "./entity-stat.service";

@Controller('entity/stat')
export class EntityStatController {
    constructor(
        private readonly entityStatService: EntityStatService
    ) {
    }

    @Get('port')
    async port(): Promise<any> {
        await this.entityStatService.portStat();
        return {
          result: true
        }
    }

}
