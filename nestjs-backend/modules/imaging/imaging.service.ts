import {Injectable} from '@nestjs/common';
import {InjectConfig} from "nestjs-config";

@Injectable()
export class ImagingService {
    constructor(
        @InjectConfig() private readonly config,
    ) {
    }

    public getImageStyles(pipe: string, src: string, sizes: string[] = [], params: any = {}, isGallery: boolean = false): any {

        let
            options = this.config.get('app.imaging') || {},
            styles = options.styles,
            storagePrefix = ''

        let parts = src.split('/')

        parts.shift();

        if (pipe == parts[0])
            storagePrefix = parts.shift()

        let srcPath = parts.join('/')

        let res = {}

        sizes = sizes.length ? sizes : Object.keys(styles)

        for (let styleId of sizes) {

            let styleOperations = styles[styleId]

            if (!Array.isArray(styleOperations)) {
                styleOperations = [styleOperations]
            }

            let cdParams = []

            cdParams.push('f_auto')

            styleOperations.forEach((item) => {

                let op = item.op
                let facefind = true

                if (item.face === false || params.facefind_disable)
                    facefind = false

                if (isGallery && params.gallery_crop_disable) {
                    op = 'resize';
                }

                switch (op) {
                    case 'crop':
                        cdParams.push('c_thumb');
                        if (facefind)
                            cdParams.push('g_custom:face');
                        cdParams.push('w_' + item.args[0]);
                        cdParams.push('h_' + item.args[1]);
                        break
                    case 'resize':
                        cdParams.push('c_limit');
                        cdParams.push('w_' + item.args[0]);
                        cdParams.push('h_' + item.args[1]);
                        break
                }
            })

            res[styleId] = {
                src: 'https://res.cloudinary.com/dq1mqtwrd/image/fetch/' + cdParams.join(',') + '/' + 'https://www.ogirk.ru' + src
                // src: '/' + ['imager', style, pipe, srcPath].join('/')
            }
        }

        return res
    }
}
