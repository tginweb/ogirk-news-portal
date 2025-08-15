import {InjectConfig} from "nestjs-config";
import {ReturnModelType} from "@typegoose/typegoose";
import {InjectModel} from "nestjs-typegoose";
import {TemplateModel} from "./template.model";

export class TemplaterService {

    constructor(
        @InjectConfig() private readonly config,
        @InjectModel(TemplateModel) private readonly templaterModel: ReturnModelType<typeof TemplateModel> | any,
    ) {
    }

    async templateToPdf(order, message, context: any = {}): Promise<void | any> {

    }


}
