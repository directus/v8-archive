import Vue from '../main';

export default function formatField(field, collection) {
	const fallback = Vue.$helpers.formatTitle(field);

	if (!field || typeof field !== 'string') {
		return fallback;
	}

	if (!collection || typeof collection !== 'string') {
		return fallback;
	}

	const i18nKey = `fields.${collection}.${field}`;

	if (Vue.$te(i18nKey)) {
		return Vue.$t(i18nKey);
	}

	return fallback;
}
