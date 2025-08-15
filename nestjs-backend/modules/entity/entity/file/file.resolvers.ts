import {Args, Info, Query, Resolver, ResolveField, Subscription, Parent, Root, CONTEXT, Context} from '@nestjs/graphql';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {
    FileModel,
    FileService,
} from "./index"
;

@Resolver('File')
export class FileResolvers {

    constructor(
        @InjectModel(FileModel) private readonly fileModel: ReturnModelType<typeof FileModel> | any,
        private readonly fileService: FileService,
    ) {

    }

    /*
    @Query('getFile')
    async getFile(parent, args, ctx, info) {
        return [];
    }
     */

}
