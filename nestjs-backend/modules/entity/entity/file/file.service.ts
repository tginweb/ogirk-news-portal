import {Injectable} from '@nestjs/common';
import {InjectModel} from "nestjs-typegoose";
import {DocumentType, ReturnModelType} from "@typegoose/typegoose";
import {FileModel} from "./file.model";
import {IDocumentQueryExtended} from "~lib/db/mongoose/interfaces/query-extended.interface";
import {dbResultsFormat} from "~lib/db/results";

@Injectable()
export class FileService {

    constructor(
        @InjectModel(FileModel) public readonly fileModel: ReturnModelType<typeof FileModel> | any,
    ) {
    }

    createValidateErrors<T>(
        data: any,
    ): any {
        let fileEntity = new this.fileModel(data);
        return fileEntity.validateSync()
    }

    async delete<T>(
        conditions: object,
    ): Promise<T[]> {
        return await this.fileModel.deleteMany(conditions);
    }

    async create<T>(
        data: any,
    ): Promise<T> {
       let fileEntity = new this.fileModel(data);
       await fileEntity.save();
       return fileEntity
    }

    async publicFindOneBy<T>(
        by: string,
        value: any,
        taxonomy?: string,
        view?: string[] | string | { view: string },
    ): Promise<T[]> {
        return this.fileModel.findOne({_id: parseInt(value)}).exec();
    }

    async findByIds<T>(
        ids: [number | string],
        view?: string[] | string | { view: string },
        by?: boolean | string
    ): Promise<T[]> {
        return dbResultsFormat(await this.fileModel.find({_id: {$in: ids}}).withView(view).exec(), by);
    }
}