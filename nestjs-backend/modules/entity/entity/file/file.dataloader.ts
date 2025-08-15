import * as DataLoader from 'dataloader'
import { Injectable } from "@nestjs/common";
import { NestDataLoader } from "~lib/interceptors/dataloader";
import { loaderCacheKeyJsonable, loaderBatchViews } from "~lib/dataloader/util";
import * as asyncReduce from 'async-es/reduce';

import {
    FileModel,
    FileService
} from "./index"

@Injectable()
export class FileLoader implements NestDataLoader<string, FileModel> {
    constructor(
        private readonly fileService: FileService
    ) {

    }

    generateDataLoader(): DataLoader<string, FileModel> {

        return new DataLoader<any, any>(async (keys: any) => {
            return loaderBatchViews(keys, (view) => {
                return this.fileService.findByIds<FileModel>(view.ids, view.view, true)
            })
        }, {
            cacheKeyFn: loaderCacheKeyJsonable
        });
    }
}
