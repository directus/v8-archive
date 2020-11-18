import * as actions from './actions';
import * as getters from './getters';
import mutations from './mutations';
import { clone } from 'lodash';

export const initialState = [];

export default {
	actions,
	mutations,
	getters,
	state: clone(initialState)
};
