import mutations from './mutations';
import { clone } from 'lodash';

export const initialState = {
	queue: []
};

export default {
	state: clone(initialState),
	mutations
};
