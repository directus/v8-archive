import Vue from 'vue';
import { keyBy, mapValues, find, forEach } from 'lodash';

import {
	RESET,
	SET_COLLECTIONS,
	ADD_COLLECTION,
	DELETE_COLLECTION,
	UPDATE_COLLECTION,
	ADD_FIELD,
	UPDATE_FIELD,
	UPDATE_FIELDS,
	REMOVE_FIELD
} from '../../mutation-types';

const mutations = {
	[RESET](state) {
		// Default state is an empty object, this will delete all properties
		Object.keys(state).forEach(key => {
			Vue.delete(state, key);
		});
	},
	[SET_COLLECTIONS](state, data) {
		const collections = mapValues(keyBy(data, 'collection'), info => {
			const statusField = find(info.fields, { interface: 'status' });
			let status_mapping =
				statusField && statusField.options && statusField.options.status_mapping;

			if (status_mapping && typeof status_mapping === 'string') {
				status_mapping = JSON.parse(status_mapping);
			}

			return {
				...info,
				status_mapping
			};
		});

		forEach(collections, (value, key) => {
			Vue.set(state, key, value);
		});
	},

	[ADD_COLLECTION](state, collection) {
		if (Object.values(collection.fields).some(field => field.type === 'status')) {
			const statusField = Object.values(collection.fields).filter(
				field => field.type === 'status'
			)[0];
			collection.status_mapping = statusField.options.status_mapping;
		}

		Vue.set(state, collection.collection, collection);
	},

	[DELETE_COLLECTION](state, collection) {
		Vue.delete(state, collection);
	},

	[UPDATE_COLLECTION](state, { collection, edits }) {
		forEach(edits, (value, key) => {
			Vue.set(state[collection], key, value);
		});
	},

	[ADD_FIELD](state, { collection, field }) {
		Vue.set(state[collection], 'fields', {
			...state[collection].fields,
			[field.field]: field
		});

		if (field.type === 'status') {
			Vue.set(state[collection], 'status_mapping', field.options.status_mapping);
		}
	},

	[UPDATE_FIELD](state, { collection, field }) {
		Vue.set(state[collection], 'fields', {
			...state[collection].fields,
			[field.field]: field
		});
	},

	[UPDATE_FIELDS](state, { collection, updates }) {
		updates.forEach(update => {
			Vue.set(state[collection].fields, update.field, {
				...state[collection].fields[update.field],
				...update
			});
		});
	},

	[REMOVE_FIELD](state, { collection, field }) {
		const clone = Object.assign({}, state[collection].fields);
		delete clone[field];
		Vue.set(state[collection], 'fields', clone);
	}
};

export default mutations;
