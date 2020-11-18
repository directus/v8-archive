import Vue from 'vue';
import { RESET, SET_USERS } from '../../mutation-types';

export default {
	[RESET](state) {
		// Default state is an empty object, this will delete all properties
		Object.keys(state).forEach(key => {
			Vue.delete(state, key);
		});
	},
	[SET_USERS](state, users) {
		Object.values(users).forEach(user => {
			Vue.set(state, user.id, user);
		});
	}
};
