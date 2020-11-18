import Vue from 'vue';
import VueI18n from 'vue-i18n';

import enUSBase from './en-US/index.json';
import enUSInterfaces from './en-US/interfaces.json';
import enUSLayouts from './en-US/layouts.json';
import dateTimeFormats from './en-US/date-format.json';

import { merge } from 'lodash';

Vue.use(VueI18n);

export const i18n = new VueI18n({
	locale: 'en-US',
	fallbackLocale: 'en-US',
	messages: {
		'en-US': merge(enUSBase, enUSInterfaces, enUSLayouts)
	},
	dateTimeFormats: {
		'en-US': dateTimeFormats
	},
	silentTranslationWarn: true
});

const loadedLanguages = ['en-US'];

/**
 * Change the language in the i18n plugin and set the HTML element's lang attribute
 * @param {string} lang The language to change to
 */
function setI18nLanguage(lang) {
	i18n.locale = lang;
	document.querySelector('html').setAttribute('lang', lang);
	return lang;
}

// List of available languages in the system
export const availableLanguages = {
	'af-ZA': 'Afrikaans (South Africa)',
	'ar-SA': 'Arabic (Saudi Arabia)',
	'ca-ES': 'Catalan (Spain)',
	'zh-CN': 'Chinese (Simplified)',
	'cs-CZ': 'Czech (Czech Republic)',
	'da-DK': 'Danish (Denmark)',
	'nl-NL': 'Dutch (Netherlands)',
	'en-US': 'English (United States)',
	'fi-FI': 'Finnish (Finland)',
	'fr-FR': 'French (France)',
	'de-DE': 'German (Germany)',
	'el-GR': 'Greek (Greece)',
	'he-IL': 'Hebrew (Israel)',
	'hu-HU': 'Hungarian (Hungary)',
	'is-IS': 'Icelandic (Iceland)',
	'id-ID': 'Indonesian (Indonesia)',
	'it-IT': 'Italian (Italy)',
	'ja-JP': 'Japanese (Japan)',
	'ko-KR': 'Korean (Korea)',
	'ms-MY': 'Malay (Malaysia)',
	'no-NO': 'Norwegian (Norway)',
	'pl-PL': 'Polish (Poland)',
	'pt-BR': 'Portuguese (Brazil)',
	'pt-PT': 'Portuguese (Portugal)',
	'ru-RU': 'Russian (Russian Federation)',
	'es-ES': 'Spanish (Spain)',
	'es-419': 'Spanish (Latin America)',
	'zh-TW': 'Taiwanese Mandarin (Taiwan)',
	'tr-TR': 'Turkish (Turkey)',
	'uk-UA': 'Ukrainian (Ukraine)',
	'vi-VN': 'Vietnamese (Vietnam)'
};

i18n.availableLanguages = availableLanguages;

/**
 * Load a new language file (if it hasn't been loaded yet) and change the system language
 *   to this new language.
 * @async
 * @param {string} lang The language to change to
 * @returns {Promise<string>} The language that was passed
 */
export async function loadLanguageAsync(lang) {
	if (!lang) return;
	if (Object.keys(availableLanguages).includes(lang) === false) return;

	if (i18n.locale !== lang) {
		if (!loadedLanguages.includes(lang)) {
			// this generates a separate chunk (lang-[request].[hash].js) for this file
			// which is lazy-loaded when the file is needed
			try {
				const msgs = await import(
					/* webpackChunkName: "lang-[request]" */ `@/lang/${lang}/index.json`
				);

				i18n.mergeLocaleMessage(lang, msgs);
			} catch {
				console.warn("Couldn't fetch language messages");
			}

			try {
				const msgs = await import(
					/* webpackChunkName: "lang-interfaces-[request]" */ `@/lang/${lang}/interfaces.json`
				);

				i18n.mergeLocaleMessage(lang, msgs);
			} catch {
				console.warn("Couldn't fetch language interface messages");
			}

			try {
				const msgs = await import(
					/* webpackChunkName: "lang-interfaces-[request]" */ `@/lang/${lang}/layouts.json`
				);

				i18n.mergeLocaleMessage(lang, msgs);
			} catch {
				console.warn("Couldn't fetch language layouts messages");
			}

			try {
				const dateTimeFormats = await import(
					/* webpackChunkName: "date-[request]" */ `@/lang/${lang}/date-format.json`
				);
				i18n.setDateTimeFormat(lang, dateTimeFormats);
			} catch {
				console.warn("Couldn't fetch date formats for language");
			}

			loadedLanguages.push(lang);
			return setI18nLanguage(lang);
		}
		return Promise.resolve(setI18nLanguage(lang));
	}
	return Promise.resolve(lang);
}
