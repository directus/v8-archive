import Vue from "vue";
import VueCompositionAPI from "@vue/composition-api";

Vue.use(VueCompositionAPI);

import "../src/design/_base.scss";
import "../src/design/_fonts.scss";
import "../src/design/_variables.scss";

import { addParameters } from '@storybook/vue';
addParameters({
	docs: {
		inlineStories: true
	}
});
