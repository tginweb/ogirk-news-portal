import { Document } from 'mongoose';

export interface PostIssue extends Document {
  readonly title: string;
  readonly type: string;
  readonly taxonomy: object;

  call1(): string;
}
