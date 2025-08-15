import {Injectable} from '@nestjs/common';


@Injectable()
export class EntityTagsService {

    public contentTags: any

    constructor() {


    }

    async reload() {
        this.contentTags = null
    }

    async getContentTags() {

        /*
        if (!this.contentTags) {

            const items = []

            const docs = await this.termService.termModel.find({nameLemmas: {$ne: null}})

            Array.prototype.push.apply(items, (docs).map((doc) => {
                return {
                    entityType: 'term',
                    entityId: doc.nid,
                    nameLemma: doc.nameLemmas.join(' '),
                    url: doc.url
                }
            }))

            this.contentTags = items.reduce((res, item) => {
                res[item.nameLemma] = item
                return res;
            }, {})
        }

         */
    }

    async findTagByNameLemma(lemma) {
        const tags = await this.getContentTags()
        return tags[lemma]
    }
}
