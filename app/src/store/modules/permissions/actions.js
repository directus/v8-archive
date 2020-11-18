import api from '../../../api';
import { defaultFull, defaultNone } from './defaults';
import { mapValues, omit, forEach } from 'lodash';
import { SET_PERMISSIONS, UPDATE_COLLECTION, ADD_PERMISSION } from '../../mutation-types';

export function addPermission({ commit }, { collection, permission }) {
	commit(ADD_PERMISSION, { collection, permission });
}

export function getPermissions({ commit, rootState }) {
	const collections = rootState.collections;
	const admin = rootState.currentUser.admin === true;
	const defaultPerm = admin ? defaultFull : defaultNone;

	const permissions = mapValues(collections, collection => {
		if (collection.status_mapping) {
			return {
				statuses: mapValues(collection.status_mapping, () => defaultPerm),
				$create: defaultPerm
			};
		}

		return {
			...defaultPerm,
			$create: defaultPerm
		};
	});

	if (admin) {
		commit(SET_PERMISSIONS, permissions);
		return Promise.resolve();
	}

	return api
		.getMyPermissions()
		.then(res => res.data)
		.then(savedPermissions => {
			savedPermissions.forEach(permission => {
				const { collection, status } = permission;

				if (status) {
					if (status === '$create') {
						return (permissions[collection].$create = permission);
					}

					return (permissions[collection].statuses[status] = permission);
				} else {
					return (permissions[collection] = {
						...permissions[collection],
						...permission
					});
				}
			});

			commit(SET_PERMISSIONS, permissions);
			return permissions;
		})
		.then(permissions => {
			forEach(permissions, permission => {
				if (permission.read_field_blacklist && permission.read_field_blacklist.length > 0) {
					const collection = collections[permission.collection];

					commit(UPDATE_COLLECTION, {
						collection: permission.collection,
						edits: {
							fields: omit(collection.fields, permission.read_field_blacklist)
						}
					});
				}
			});
		});
}
