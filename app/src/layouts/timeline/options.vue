<template>
	<form @submit.prevent>
		<label for="spacing" class="type-label">
			{{ $t('layouts.timeline.date') }}
			<v-icon class="required" name="star" color="--input-required-color" sup />
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.date || '__none__'"
			:options="dateOptions"
			class="select"
			icon="today"
			@input="setOption('date', $event)"
		></v-select>
		<label for="spacing" class="type-label">
			{{ $t('layouts.timeline.title') }}
			<v-icon class="required" name="star" color="--input-required-color" sup />
		</label>
		<v-input
			id="spacing"
			:pattern="titleValidator"
			:value="viewOptions.title"
			:options="textOptions"
			@input="setOption('title', $event)"
		></v-input>
		<label for="spacing" class="type-label">
			{{ $t('layouts.timeline.content') }}
			<v-icon class="required" name="star" color="--input-required-color" sup />
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.content || '__none__'"
			:options="contentOptions"
			class="select"
			icon="title"
			@input="setOption('content', $event)"
		></v-select>
		<label for="spacing" class="type-label">
			{{ $t('layouts.timeline.color') }}
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.color || '__none__'"
			:options="textOptions"
			class="select"
			icon="color_lens"
			@input="setOption('color', $event)"
		></v-select>
	</form>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/layout';
import { mapValues, pickBy, keys } from 'lodash';

export default {
	mixins: [mixin],
	data() {
		return {
			sortList: null,
			titleValid: true
		};
	},
	computed: {
		contentOptions() {
			var options = {
				__none__: `(${this.$t('dont_show')})`,
				...mapValues(this.fields, info =>
					['integer', 'string', 'user'].includes(info.type) ? info.name : null
				)
			};
			return pickBy(options, identity);
		},
		titleValidator() {
			var fields = keys(this.fields); //this.getKeys(this.fields)
			fields = fields
				.toString()
				.replace('"', '')
				.replace(/,/g, '|');
			var regex = new RegExp(
				'^([^\\{\\}]*?\\{\\{\\s*?(' + fields + ')\\s*?\\}\\})*?[^\\{\\}]*?$'
			);
			return regex.toString().replace(/\//g, '');
		},
		textOptions() {
			var options = {
				__none__: `(${this.$t('dont_show')})`,
				...mapValues(this.fields, info =>
					info.type == 'string' || info.type == 'integer' ? info.name : null
				)
			};
			return pickBy(options, identity);
		},
		dateOptions() {
			var options = mapValues(this.fields, info =>
				['date', 'datetime'].includes(info.type) ? info.name : null
			);
			return pickBy(options, identity);
		}
	},
	methods: {
		getKeys(obj) {
			var keys = keys(obj);
			var subKeys = [];
			for (var i = 0; i < keys.length; i++) {
				if (typeof obj[keys[i]] === 'object') {
					var subKeyList = this.getKeys(obj[keys[i]]);
					for (var k = 0; k < subKeyList.length; k++) {
						subKeys.push(keys[i] + '.' + subKeyList[k]);
					}
				}
			}
			return [...keys, ...subKeys];
		},
		setOption(option, value) {
			this.$emit('options', {
				...this.viewOptions,
				[option]: value
			});
		}
	}
};
</script>

<style lang="scss" scoped>
.type-label {
	margin-top: var(--form-vertical-gap);
	margin-bottom: var(--input-label-margin);
	&:first-of-type {
		margin-top: 0;
	}
}
</style>
