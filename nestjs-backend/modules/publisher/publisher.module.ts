import {Module} from '@nestjs/common';

import {PubSub} from 'graphql-subscriptions';
import {PublisherService} from './publisher.service';

@Module({
    providers: [
        PublisherService,
        {
            provide: 'PUBSUB',
            useFactory: () => {
                // Use a factory to add you custom options
                return new PubSub({});
            },
        },
    ],
    exports: [
        PublisherService,
    ],
})
export class PublisherModule {}
