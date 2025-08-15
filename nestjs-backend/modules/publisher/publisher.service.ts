import {Inject, Injectable} from '@nestjs/common';
import {PubSub} from 'graphql-subscriptions';

@Injectable()
export class PublisherService {
    constructor(
        @Inject('PUBSUB') public readonly pubsub: PubSub
    ) {
    }

    public async publish(event, payload): Promise<any> {
        await this.pubsub.publish(event, payload);
    }
}

