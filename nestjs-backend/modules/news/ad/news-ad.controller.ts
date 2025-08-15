import {Body, Controller, Get, Post} from '@nestjs/common';

@Controller('entity/ad')
export class NewsAdController {
    constructor(

    ) {
    }

    @Get('redirect')
    async redirect(): Promise<any> {

        return 'zzzsss';
    }
}
