/* global require */

import { forEach, isObject, mapValues } from 'lodash';
import api from '../../../api';
import { i18n } from '../../../lang/';

import * as mutationTypes from '../../mutation-types';

/**
 * Recursively loop over object values and replace each string value that starts with $t: with it's
 *   translation as managed by vue-i18n
 * @param  {Object} meta Object to loop over
 * @param  {String} type Extension type
 * @param  {String} id   Extension ID
 * @return {Object}      Formatted object
 */
function translateFields(meta, type, id) {
	const format = value => {
		if (value == null) return value;

		if (typeof value === 'string') {
			// split up the sentence into separate words to allow multiple translations in one value
			//   like $t:option $t:or $t:value
			return value
				.split(' ')
				.map(word =>
					word.startsWith('$t:') ? i18n.t(`${type}.${id}.${word.substring(3)}`) : word
				)
				.join(' ');
		}

		if (
			(isObject(value) && Object.keys(value).length > 0) ||
			(Array.isArray(value) && value.length > 0)
		) {
			return translateFields(value, type, id);
		}

		return value;
	};

	return Array.isArray(meta) ? meta.map(format) : mapValues(meta, format);
}

/**
 * Read all the meta.json files of the provided extension type and return them
 * as an object ready to be put into the store.
 * @param  {String} type - Type of extension to read
 * @return {Array}       - Meta information for all extensions of the given type
 */
function readCoreExtensions(type) {
	let requireContext;

	switch (type) {
		case 'interfaces':
			requireContext = require.context('@/interfaces/', true, /meta.json$/);
			break;
		case 'layouts':
			requireContext = require.context('@/layouts/', true, /meta.json$/);
			break;
		case 'modules':
			requireContext = require.context('@/modules/', true, /meta.json$/);
			break;
	}

	return requireContext.keys().map(readMeta);

	function readMeta(fileName) {
		const metaDefinition = requireContext(fileName);

		const extensionId = fileName
			.replace(/^\.\//, '') // remove the ./ from the beginning
			.replace(/\.\w+$/, '') // remove the extension from the end
			.split(/\//)[0];

		return {
			...metaDefinition,
			id: extensionId,
			// Mark them as core interfaces so the app won't try to fetch them from
			// the server
			core: true
		};
	}
}

/**
 * Get extensions by type
 * @param  {Function} commit Vuex Commit function
 * @param  {String} type     Extension type to fetch records of
 * @return {[type]}        [description
 */
export function getExtensions({ commit }, type) {
	const typeUpper = type.toUpperCase();

	let request = null;

	switch (type) {
		case 'interfaces':
			request = api.getInterfaces();
			break;
		case 'layouts':
			request = api.getLayouts();
			break;
		case 'modules':
			request = api.getModules();
			break;
		default:
	}

	return (
		request
			.then(res => res.data)

			/**
      Merge all available translations into vue-i18n

      Prefix by type and id
    */
			.then(extensions => {
				extensions = [...extensions, ...readCoreExtensions(type)];

				extensions.forEach(extension => {
					const { id, translation } = extension;

					if (!translation) return;

					const translations = {};

					// Translations are in the following format
					//
					// {
					//   [locale]: {
					//     [key]: value
					//   }
					// }
					//
					// we have to scope them to [type].[interface].key before merging
					// them into the global i18n messages pool
					forEach(translation, (messages, locale) => {
						translations[locale] = {
							[type]: {
								[id]: messages
							}
						};
					});

					Object.keys(translations).forEach(locale => {
						i18n.mergeLocaleMessage(locale, translations[locale]);
					});
				});

				return extensions;
			})

			/**
			 * Replace all to-be-translated strings (prefixed with $t: )
			 *   with previously registered translations
			 */
			.then(extensions =>
				extensions.map(extension => translateFields(extension, type, extension.id))
			)

			/**
			 * Commit parsed extensions to the store
			 */
			.then(extensions => {
				commit(mutationTypes[`SET_${typeUpper}`], extensions);
			})
	);
}

/**
 * Get all extensions
 * @param  {Function} dispatch Vuex dispatch function
 * @return {Promise}           Fires getExtensions for each extension type
 */
export function getAllExtensions({ dispatch }) {
	return Promise.all([
		dispatch('getExtensions', 'interfaces'),
		dispatch('getExtensions', 'layouts'),
		dispatch('getExtensions', 'modules')
	]);
}
