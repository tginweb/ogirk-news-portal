import {prop, Ref} from "@typegoose/typegoose";

export class PostStatData {
  @prop({default: 0})
  views: number;
}
