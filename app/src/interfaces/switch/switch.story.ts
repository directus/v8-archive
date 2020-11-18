import {
	withKnobs,
	text,
	boolean,
	number,
	optionsKnob as options,
	color
} from '@storybook/addon-knobs';
import { action } from '@storybook/addon-actions';
import Vue from 'vue';
import Input from './input.vue';
import Display from './display.vue';

Vue.component('input-switch', Input);
Vue.component('display-switch', Display);

export default {
	title: 'Interfaces / Switch',
	component: Input,
	decorators: [withKnobs]
};

export const input = () => ({
	data() {
		return {
			checked: false
		};
	},
	methods: {
		onInput: action('input')
	},
	props: {
		readonly: {
			default: boolean('Readonly', false, 'State')
		},
		options: {
			default: {
				labelOff: text('Label (off)', '', 'Options'),
				labelOn: text('Label (on)', '', 'Options'),
				checkbox: boolean('Show as checkbox', false, 'Options')
			}
		}
	},
	template: `<input-switch :readonly="readonly" :options="options" v-model="checked" @input="onInput" />`
});

export const display = () => ({
	props: {
		value: {
			default: boolean('Value', false, 'State')
		},
		options: {
			default: {
				checkbox: boolean('Show as checkbox', false, 'Options')
			}
		}
	},
	template: `<display-switch :options="options" :value="value" />`
});
