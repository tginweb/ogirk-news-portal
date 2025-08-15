import { Provider } from '@nestjs/common';
//import { PostIssueSchema } from './entity/post-issue/post-issue.schema';

export const entityProviders: Provider[] = [
	{
		provide: 'MY-TOKEN',
		useValue: 'my-injected-value',
		//provide: 'PostModel',
		//useFactory: (postModel) => postModel.discriminator('sm-issue-print', PostIssueSchema),
		//inject: [ getModelToken('Post') ]
	}
];
