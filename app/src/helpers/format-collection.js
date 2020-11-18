import Vue from '../main';

export default function formatCollection(collection) {
	const fallback = Vue.$helpers.formatTitle(collection);

	if (!collection || typeof collection !== 'string') {
		console.warn('[formatField]: Expected collection to be a string');
		return fallback;
	}

	const i18nKey = `collections.${collection}`;

	if (Vue.$te(i18nKey)) {
		return Vue.$t(i18nKey);
	}

	return fallback;
}
