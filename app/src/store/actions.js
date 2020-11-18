import api from '../api';
import axios from 'axios';
import router from '@/router';
import hydrateStore from '@/hydrate';
import { loadLanguageAsync } from '@/lang/';
import isCloudProject from '@/helpers/is-cloud-project';

import {
	RESET,
	LATENCY,
	SET_CURRENT_USER,
	UPDATE_CURRENT_USER,
	SET_BOOKMARKS,
	ADD_BOOKMARK,
	DELETE_BOOKMARK,
	LOADING_START,
	LOADING_FINISHED,
	SET_PROJECT_STATUS,
	UPDATE_PROJECT,
	SET_CURRENT_PROJECT,
	INIT_PROJECTS
} from './mutation-types';

export function latency({ commit }) {
	const start = performance.now();
	const now = Date.now();

	api.ping()
		.then(() => {
			const end = performance.now();
			const delta = end - start;
			commit(LATENCY, {
				date: now,
				latency: delta
			});
		})
		.catch(error => {
			const end = performance.now();
			const delta = end - start;

			if (error.code === -2) {
				return commit(LATENCY, {
					date: now,
					latency: delta
				});
			}

			return commit(LATENCY, {
				date: now,
				latency: -1
			});
		});
}

export function getCurrentUser({ commit }) {
	return api
		.getMe({
			fields: [
				'id',
				'avatar.*',
				'email',
				'first_name',
				'last_name',
				'locale',
				'role.*',
				'last_page',
				'theme'
			]
		})
		.then(res => res.data)
		.then(userInfo => {
			return {
				...userInfo,
				admin: userInfo.role.id === 1
			};
		})
		.then(data => commit(SET_CURRENT_USER, data));
}

export function track({ commit, state }, { page }) {
	const currentUserID = state.currentUser.id;

	if (!currentUserID) {
		return;
	}

	const data = {
		last_page: page
	};

	commit(UPDATE_CURRENT_USER, data);
	return api.api.request('PATCH', `/users/${currentUserID}/tracking/page`, {}, data);
}

export function getBookmarks({ commit }) {
	return api.getMyBookmarks().then(bookmarks => {
		commit(SET_BOOKMARKS, bookmarks);
		return bookmarks;
	});
}

export function saveBookmark({ commit }, bookmark) {
	return api.createCollectionPreset(bookmark).then(res => {
		const savedBookmark = res.data;
		commit(ADD_BOOKMARK, savedBookmark);
		return savedBookmark;
	});
}

export function deleteBookmark({ commit }, bookmarkID) {
	commit(DELETE_BOOKMARK, bookmarkID);
	return api.deleteCollectionPreset(bookmarkID).catch(error => {
		this.$events.emit('error', {
			notify: this.$t('something_went_wrong_body'),
			error
		});
	});
}

export function loadingStart({ commit }, { id, desc }) {
	commit(LOADING_START, { id, desc });
}

export function loadingFinished({ commit }, id) {
	commit(LOADING_FINISHED, id);
}

export async function setCurrentProject({ commit, dispatch, state, getters }, key) {
	let newProject = state.projects.find(p => p.key === key);

	// If the project we're trying to switch to doesn't exist in the projects array, there is a
	// chance it's a hidden project. We're attempting to fetch the project info for the new key
	// regardless to make sure you can login to private projects too. If the project doesn't exist,
	// the user will see a "something is wrong with the project" error on the login screen, as the status
	// of the project will be "failed"
	if (!newProject) {
		await dispatch('updateProjectInfo', key);
		newProject = state.projects.find(p => p.key === key);
	}

	commit(SET_CURRENT_PROJECT, key);

	const authenticated = newProject.data?.authenticated || false;
	const privateRoute = router.currentRoute.meta.publicRoute !== true;

	const locale = getters.currentProject.data?.default_locale;

	if (locale) {
		loadLanguageAsync(locale);
	}

	if (privateRoute && authenticated) {
		commit(RESET);
		await dispatch('getProjects');

		if (authenticated) {
			await hydrateStore();

			// Default to /collections as homepage
			let route = `/${key}/collections`;

			// If the last visited page is saved in the current user record, use that
			if (state.currentUser.last_page) {
				route = state.currentUser.last_page;
			}

			router.push(route);
		}
	} else if (privateRoute && authenticated === false) {
		router.push('/login');
	}
}

export async function updateProjectInfo({ commit, state }, key) {
	const apiRootPath = state.apiRootPath;
	const url = apiRootPath + key + '/';
	commit(SET_PROJECT_STATUS, { key: key, status: 'loading' });

	try {
		const response = await axios.get(url);
		const {
			project_name,
			project_foreground,
			project_color,
			project_background,
			project_logo,
			project_public_note,
			telemetry,
			default_locale
		} = response.data.data.api;
		const authenticated = response.data.public === undefined;

		commit(UPDATE_PROJECT, {
			key: key,
			data: {
				project_name,
				project_foreground,
				project_color,
				project_background,
				project_logo,
				project_public_note,
				telemetry,
				default_locale,
				authenticated
			}
		});
		commit(SET_PROJECT_STATUS, { key: key, status: 'successful' });
	} catch (error) {
		commit(UPDATE_PROJECT, { key: key, error });
		commit(SET_PROJECT_STATUS, { key: key, status: 'failed' });
	}
}

export async function getProjects({ state, dispatch, commit }, force) {
	const currentProjectKey = state.currentProjectKey;
	const apiRootPath = state.apiRootPath;

	if (force === true || state.projects === null || state.projects === false) {
		const url = apiRootPath + 'server/projects';

		try {
			const response = await axios.get(url);
			const projectKeys = response.data.data;

			const projects = projectKeys.map(key => ({
				key,
				status: null,
				data: null,
				error: null
			}));

			// If a currentProjectKey is set that doesn't exist in the projects returned by the project endpoint
			// it's most likely because there is a private project with this key.
			if (currentProjectKey && projectKeys.includes(currentProjectKey) === false) {
				projects.push({
					key: currentProjectKey,
					status: null,
					data: null,
					error: null
				});
			}

			commit(INIT_PROJECTS, projects);
		} catch (error) {
			const errorCode = error.response?.data.error.code;

			if (errorCode === 14) {
				commit(INIT_PROJECTS, false);
			}
		}
	}

	// CLOUD
	// If the project is a Directus Cloud project, fetch the project keys from the Cloud API instead
	if (isCloudProject(currentProjectKey)) {
		console.log('[Cloud] Using cloud projects');
		const url = `https://cloud-api.directus.cloud/projects/${currentProjectKey}/related`;
		try {
			const response = await axios.get(url);
			const projectKeys = response.data.data;

			const projects = projectKeys.map(key => ({
				key,
				status: null,
				data: null,
				error: null
			}));

			commit(INIT_PROJECTS, projects);
		} catch (error) {
			// If the response failed, it means that the project key doesn't exist in cloud
			console.log(error);
		}
	}

	// If there's no pre-selected project, default to the first available one in the projects array
	if (state.projects?.length > 0 && currentProjectKey === null) {
		dispatch('setCurrentProject', state.projects[0].key);
	}

	if (state.projects !== null && state.projects !== false) {
		// Fetch the detailed information for each project asynchronously.
		return Promise.allSettled(
			state.projects.map(p => p.key).map(key => dispatch('updateProjectInfo', key))
		);
	}

	return Promise.resolve();
}
