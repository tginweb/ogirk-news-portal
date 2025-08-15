import {Injectable} from '@nestjs/common'
import {HttpService} from '@nestjs/axios'
import {InjectConfig} from "nestjs-config"

@Injectable()
export class PorterService {
    constructor(
        @InjectConfig() private readonly config,
        private httpService: HttpService
    ) {
    }

    public async portSourceRequest(method, name, params: any = {}): Promise<any> {

        const api = this.config.get('app.PORTER_SOURCE_API')
        const methodUrl = api.URL + '/' + name
        let res

        params = {
            ...params,
            apiKey: api.KEY
        }

        const config: any = {
            headers: {
                'Authorisation': 'Bearer ' + api.KEY
            }
        }

        switch (method) {
            case 'GET':
                config.params = params
                res = await this.httpService.get(methodUrl, config).toPromise()
                break;
            case 'POST':
                res = await this.httpService.post(methodUrl, params, config).toPromise()
                break;
        }

        return res && res.data
    }
}

