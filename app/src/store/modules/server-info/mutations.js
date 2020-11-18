import { RESET, SET_SERVER_INFO } from '../../mutation-types';
import { initialState } from './';

export default {
	[RESET](state) {
		Object.keys(initialState).forEach(key => {
			state[key] = initialState[key];
		});
	},
	[SET_SERVER_INFO](state, info) {
		state.apiVersion = info.apiVersion;
		state.phpVersion = info.phpVersion;
		state.maxUploadSize = info.maxUploadSize;
	}
};
