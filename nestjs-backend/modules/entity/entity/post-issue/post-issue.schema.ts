import { Schema } from 'mongoose';

export const PostIssueSchema: Schema = new Schema(
	{
		friendly: {
			type: Boolean,
			default: true
		}
	},
	{
		discriminatorKey: 'type',
	}
);

PostIssueSchema.set('toJSON', {
	transform: function (doc, ret) {

	}
})

PostIssueSchema.methods.getClass = function(cb) {
	return 'issue';
};
