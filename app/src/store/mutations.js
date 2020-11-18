import {
	STORE_HYDRATED,
	HYDRATING_FAILED,
	LATENCY,
	SET_CURRENT_USER,
	UPDATE_CURRENT_USER,
	SET_BOOKMARKS,
	ADD_BOOKMARK,
	DELETE_BOOKMARK,
	TOGGLE_NAV,
	TOGGLE_INFO,
	LOADING_START,
	LOADING_FINISHED,
	SET_CURRENT_PROJECT,
	UPDATE_PROJECT,
	SET_PROJECT_STATUS,
	INIT_PROJECTS
} from './mutation-types';
import Vue from 'vue';

const mutations = {
	[STORE_HYDRATED](state, date) {
		state.hydrated = date;
	},

	[HYDRATING_FAILED](state, error) {
		state.hydrated = false;
		state.hydratingError = error;
	},

	[LATENCY](state, info) {
		const latencies = [...state.latency];
		latencies.push(info);

		if (latencies.length > 200) {
			latencies.shift();
		}

		state.latency = latencies;
	},

	[SET_CURRENT_USER](state, data) {
		state.currentUser = data;
	},

	[UPDATE_CURRENT_USER](state, data) {
		state.currentUser = {
			...state.currentUser,
			...data
		};
	},

	[SET_BOOKMARKS](state, data) {
		state.bookmarks = data;
	},

	[ADD_BOOKMARK](state, bookmark) {
		state.bookmarks = [...state.bookmarks, bookmark];
	},

	[DELETE_BOOKMARK](state, id) {
		state.bookmarks = state.bookmarks.filter(bookmark => bookmark.id !== id);
	},

	[TOGGLE_NAV](state, active = !state.sidebars.nav) {
		state.sidebars.nav = active;
	},

	[TOGGLE_INFO](state, active = !state.sidebars.info) {
		state.sidebars.info = active;
	},

	[LOADING_START](state, { id, desc }) {
		state.queue = [...state.queue, { id, desc }];
	},

	[LOADING_FINISHED](state, id) {
		state.queue = state.queue.filter(req => req.id !== id);
	},

	[SET_CURRENT_PROJECT](state, key) {
		state.currentProjectKey = key;
	},

	[UPDATE_PROJECT](state, { key, data, error }) {
		const index = state.projects.findIndex(p => p.key === key);

		if (index !== -1) {
			Vue.set(state.projects, index, {
				...state.projects[index],
				data: {
					...state.projects[index].data,
					...(data || {})
				},
				error: error || null
			});
		} else {
			state.projects = [
				...state.projects,
				{
					key,
					data,
					error
				}
			];
		}
	},

	[SET_PROJECT_STATUS](state, { key, status }) {
		const index = state.projects.findIndex(p => p.key === key);
		Vue.set(state.projects, index, {
			...state.projects[index],
			status
		});
	},

	[INIT_PROJECTS](state, projects) {
		state.projects = projects;
	}
};

export default mutations;
