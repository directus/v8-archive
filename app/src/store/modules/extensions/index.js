import * as actions from './actions';
import mutations from './mutations';
import { clone } from 'lodash';

export const initialState = {
	layouts: {},
	interfaces: {},
	modules: {}
};

export default {
	state: clone(initialState),
	actions,
	mutations
};
