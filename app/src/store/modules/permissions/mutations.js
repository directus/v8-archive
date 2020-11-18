import Vue from 'vue';
import { forEach } from 'lodash';
import { RESET, SET_PERMISSIONS, ADD_PERMISSION } from '../../mutation-types';

const mutations = {
	[RESET](state) {
		// Default state is an empty object, this will delete all properties
		Object.keys(state).forEach(key => {
			Vue.delete(state, key);
		});
	},
	[SET_PERMISSIONS](state, permissions) {
		forEach(permissions, (permission, collection) => {
			Vue.set(state, collection, permission);
		});
	},
	[ADD_PERMISSION](state, { collection, permission }) {
		Vue.set(state, collection, permission);
	}
};

export default mutations;
