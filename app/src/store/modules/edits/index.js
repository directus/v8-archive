import * as actions from './actions';
import * as getters from './getters';
import mutations from './mutations';
import { clone } from 'lodash';

export const initialState = {
	collection: null,
	primaryKey: null,
	values: {},
	savedValues: {}
};

export default {
	actions,
	mutations,
	state: clone(initialState),
	getters
};
