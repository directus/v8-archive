import _ from 'lodash';
import Vue from 'vue';
import { RESET, SET_SETTINGS, SET_SETTING } from '@/store/mutation-types';
import { initialState } from './';
import { keyBy, mapValues } from 'lodash';

export default {
	[RESET](state) {
		Object.keys(initialState).forEach(key => {
			state[key] = initialState[key];
		});
	},

	[SET_SETTINGS](state, settings) {
		const settingsByKey = keyBy(settings, 'key');
		state.values = mapValues(settingsByKey, 'value');
		state.primaryKeys = mapValues(settingsByKey, 'id');
	},

	[SET_SETTING](state, { id, key, value }) {
		Vue.set(state.values, key, value);
		Vue.set(state.primaryKeys, key, id);
	}
};
