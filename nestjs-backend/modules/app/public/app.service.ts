import { Injectable} from '@nestjs/common';
import {Interval} from '@nestjs/schedule';
import {PorterService} from "~modules/porter/porter.service";

@Injectable()
export class AppService {

    constructor(
        private porterService: PorterService
    ) {
    }

    public async cron2m(): Promise<any> {

    }

    public async cron5m(): Promise<any> {


    }

    public async cron10m(): Promise<any> {


    }

    public async cron30m(): Promise<any> {


    }

    public async cron1h(): Promise<any> {


    }

    public async cron24h(): Promise<any> {


    }
}

