import {Controller, Get} from '@nestjs/common';

@Controller('service')
export class ServiceController {

    constructor() {
    }

    @Get('info')
    async info(): Promise<any> {

        const packageInfo = require(process.cwd() + '/package.json')

        let info = {
            name: packageInfo.name,
            version: process.env.npm_package_version
        }

        return info
    }


}
