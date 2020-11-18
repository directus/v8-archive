import { RESET, PUSH_NOTIFICATION, REMOVE_NOTIFICATION } from '../../mutation-types';

const mutations = {
	[RESET](state) {
		state.queue = [];
	},
	[PUSH_NOTIFICATION](state, data) {
		state.queue.push(data);
	},
	[REMOVE_NOTIFICATION](state, id) {
		state.queue = state.queue.filter(item => item.id !== id);
	}
};

export default mutations;
