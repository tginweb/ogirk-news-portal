import {Body, Controller, Get, Post} from '@nestjs/common';
import {PublisherService} from "~modules/publisher/publisher.service";
import {PostService} from "~modules/entity/entity/post/post.service";
import {TermService} from "~modules/entity/entity/term/term.service";

@Controller('porter')
export class PorterController {
    constructor(
        private readonly termService: TermService,
        private readonly postService: PostService,
        private readonly publisherService: PublisherService,
    ) {
    }

    @Post('port/insert')
    async portInsert(@Body('port') port: any): Promise<any> {

        let entityService = null;

        switch (port.entity_type) {
            case 'post':
                entityService = this.postService
                break;
            case 'term':
                entityService = this.termService
                break;
        }

        const entity = await entityService.findOne({input: {filter: {nid: port.entity_id}}})

        if (entity) {
            await this.publisherService.publish('entity/insert/' + port.entity_type + '/' + entity.type, entity)
        }

        return port;
    }

}
