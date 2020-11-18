import axios from 'axios';
import store from './store/';
import { loadLanguageAsync } from './lang/';
import { STORE_HYDRATED, HYDRATING_FAILED } from './store/mutation-types';
import { version } from '../package.json';

export default async function hydrateStore() {
	try {
		await Promise.all([store.dispatch('getProjects'), store.dispatch('getCurrentUser')]);

		// getAllExtensions action will translate some values. We have to make sure to fetch the locales
		// before fetching the extensions
		const defaultLocale = store.getters.currentProject?.data?.default_locale;
		const userLocale = store.state.currentUser.locale;

		if (userLocale) {
			await loadLanguageAsync(userLocale);
		} else if (defaultLocale) {
			await loadLanguageAsync(defaultLocale);
		} else {
			await loadLanguageAsync(window.navigator.userLanguage || window.navigator.language);
		}

		await Promise.all([
			store.dispatch('latency'),
			store.dispatch('getAllExtensions'),
			store.dispatch('getCollections'),
			store.dispatch('getSettings'),
			store.dispatch('getBookmarks'),
			store.dispatch('getUsers'),
			store.dispatch('getRelations'),
			store.dispatch('getServerInfo')
		]);

		// getPermissions relies on collection info to exist
		await store.dispatch('getPermissions');

		const isAdmin = store.state.currentUser.admin;
		const telemetryAllowed = store.getters.currentProject?.data?.telemetry !== false;

		if (telemetryAllowed && isAdmin && navigator.onLine) {
			try {
				await axios.post('https://telemetry.directus.io/count', {
					type: 'app',
					url: window.location.origin,
					version
				});
			} catch {
				console.log("Couldn't send telemetery ping");
			}
		}

		store.commit(STORE_HYDRATED, new Date());
	} catch (error) {
		store.commit(HYDRATING_FAILED, error);
		console.error(error); // eslint-disable-line no-console
	}
}
