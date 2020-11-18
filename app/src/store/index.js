import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import api from '@/api';

import extensions from './modules/extensions';
import collections from './modules/collections';
import edits from './modules/edits';
import permissions from './modules/permissions';
import settings from './modules/settings';
import users from './modules/users';
import relations from './modules/relations';
import serverInfo from './modules/server-info';
import notifications from './modules/notifications';

import initialState from './state';
import * as actions from './actions';
import * as getters from './getters';
import mutations from './mutations';
import { RESET } from './mutation-types';

import { clone } from 'lodash';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production'; // eslint-disable-line no-undef

const store = new Vuex.Store({
	state: clone(initialState),
	actions,
	getters,
	mutations: {
		[RESET](state) {
			// Some parts of the state are system wide and don't have to / shouldn't be reset
			const protectedKeys = ['latency', 'currentProjectKey', 'projects'];

			Object.keys(initialState).forEach(key => {
				if (protectedKeys.includes(key)) return;
				state[key] = initialState[key];
			});
		},
		...mutations
	},
	strict: debug,
	modules: {
		collections,
		extensions,
		edits,
		permissions,
		users,
		relations,
		serverInfo,
		notifications,
		settings
	},
	plugins: [
		createPersistedState({
			key: 'directus-app',
			paths: ['currentProjectKey'],
			storage: window.sessionStorage,
			rehydrated: store => {
				api.config.project = store.state.currentProjectKey;
			}
		})
	]
});

export default store;
