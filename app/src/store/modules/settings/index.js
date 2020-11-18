import mutations from './mutations';
import * as actions from './actions';
import { clone } from 'lodash';

export const initialState = {
	values: {},
	primaryKeys: {}
};

export default {
	actions,
	mutations,
	state: clone(initialState)
};
