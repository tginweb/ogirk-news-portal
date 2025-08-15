import {BadRequestException, Controller, Get, Query, Response} from '@nestjs/common';
import {ImagingService} from './imaging.service'
import {InjectConfig} from "nestjs-config";
import * as fs from 'fs'

const mime = require('mime')


@Controller('imaging')
export class ImagingController {
    constructor(
        @InjectConfig() private readonly config,
        private readonly service: ImagingService,
    ) {
    }

    @Get('info')
    info(): string {
        return 'info';
    }



    @Get('screenshot')
    async screenshot(
        @Query('url') queryUrl,
        @Query('width') queryWidth,
        @Query('crop') queryCrop,
        @Response() response
    ) {
        const options = this.config.get('app.imaging') || {}

        //data = JSON.parse(Buffer.from(data, 'base64').toString('ascii'))

        let data: any = {
            width: queryWidth,
            crop: queryCrop
        }

        data = {
            auth: '11335f-41f32ad7d5a2d2e2b9d3c64b437441f6',
            noanimate: '',
            ...data
        }

        let apiParamsStr = Object.keys(data).map(key => {
            return data[key]==='' ? key : key + '/' + data[key]
        }).join('/')

        const apiUrl = 'https://image.thum.io/get/' + apiParamsStr + '/' + queryUrl

        //https://image.thum.io/get/auth/11335f-41f32ad7d5a2d2e2b9d3c64b437441f6/width/800/crop/400/http://vsp.ru/info1.php

        console.log(apiUrl)

        const destFilepath = options.cachePath + '/logo.png'
        const stream = fs.createReadStream(destFilepath);
        response.setHeader("Content-Type", mime.lookup(destFilepath));
        stream.pipe(response);
    }
}
