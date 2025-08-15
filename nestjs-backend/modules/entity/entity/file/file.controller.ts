import {
    Body,
    Controller,
    Get,
    HttpException, NotFoundException,
    Post,
    Query,
    Response,
    UploadedFile,
    UploadedFiles,
    UseInterceptors
} from '@nestjs/common';
import {FileInterceptor, FilesInterceptor} from '@nestjs/platform-express';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {FileModel} from "./file.model";
import * as fs from 'fs'

const path = require('path')
const mime = require('mime')

@Controller('file')
export class FileController {
    constructor(
        @InjectModel(FileModel) private readonly fileModel: ReturnModelType<typeof FileModel> | any,
    ) {
    }




    @Get('download')
    async download(@Query('id') fileId: any, @Response() response) {

        let fileEntity = await this.fileModel.findOne({_id: fileId}).exec();

        if (!fileEntity) throw new NotFoundException();

        try {

            let filepath = path.join(process.cwd(), 'uploads/' + fileEntity.filename)

            await fs.promises.access(filepath);

            const data = fs.createReadStream(filepath);

            response.setHeader("Content-Type", mime.lookup(filepath));

            data.pipe(response);

        } catch (e) {

            throw new NotFoundException();
        }

    }

    @Post('upload')
    @UseInterceptors(FileInterceptor('file'))
    async upload(@UploadedFile() file): Promise<any> {

        throw new HttpException('fff', 500);

        let data = {
            mimetype: file.mimetype,
            originalname: file.originalname
        }

        const newFile = new this.fileModel(data);

        newFile.save();

        return newFile;
    }

    @Post('uploadMultiple')
    @UseInterceptors(FilesInterceptor('files'))
    uploadMultiple(@UploadedFiles() files): string {


        return 'info';
    }

}
