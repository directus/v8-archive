import { keyBy } from 'lodash';
import { RESET, SET_INTERFACES, SET_LAYOUTS, SET_MODULES } from '../../mutation-types';
import { initialState } from './';

const mutations = {
	[RESET](state) {
		Object.keys(initialState).forEach(key => {
			state[key] = initialState[key];
		});
	},

	[SET_INTERFACES](state, interfaces) {
		state.interfaces = keyBy(interfaces, 'id');
	},
	[SET_LAYOUTS](state, layouts) {
		state.layouts = keyBy(layouts, 'id');
	},
	[SET_MODULES](state, modules) {
		state.modules = keyBy(modules, 'id');
	}
};

export default mutations;
