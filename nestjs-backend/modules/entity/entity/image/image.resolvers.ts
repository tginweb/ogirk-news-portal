import {Args, Info, Mutation, Parent, Query, ResolveField, Resolver} from '@nestjs/graphql';
import {InjectModel} from "nestjs-typegoose";
import {ReturnModelType} from "@typegoose/typegoose";
import {Loader} from "~lib/interceptors/dataloader";
import * as DataLoader from "dataloader";
import {ImagingService} from "~modules/imaging/imaging.service";

@Resolver('Image')
export class ImageResolvers {

    constructor(
        private readonly imagingService: ImagingService,
    ) {

    }

    @ResolveField()
    async thumbs(
        @Args() args,
        @Parent() parent,
        @Info() info
    ) {
        if (!parent) return {}

        let sizes = [];

        if (args.size) sizes.push(args.size)
        if (args.sizes && args.sizes.length) sizes = sizes.concat(args.sizes)

        let res = this.imagingService.getImageStyles('uploads', parent.src, sizes, parent, args.gallery)

        if (args.size) {
            res._size  = res[args.size]
            res._size.sizeId = args.size
        }

        return res
    }
}
