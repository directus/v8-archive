<template>
	<form @submit.prevent>
		<label
			for="spacing"
			class="type-label"
			:class="{ required: viewOptions.date === '__none__' }"
		>
			{{ $t('layouts.calendar.datetime') }}
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.datetime || '__none__'"
			:options="datetimeOptions"
			class="select"
			icon="access_time"
			@input="setOption('datetime', $event)"
		></v-select>
		<label
			for="spacing"
			class="type-label"
			:class="{ required: viewOptions.datetime === '__none__' }"
		>
			{{ $t('layouts.calendar.date') }}
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
			{{ $t('layouts.calendar.time') }}
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.time || '__none__'"
			:options="timeOptions"
			class="select"
			icon="schedule"
			@input="setOption('time', $event)"
		></v-select>
		<label for="spacing" class="type-label required">
			{{ $t('layouts.calendar.title') }}
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.title || '__none__'"
			:options="textOptions"
			class="select"
			icon="title"
			@input="setOption('title', $event)"
		></v-select>
		<label for="spacing" class="type-label">
			{{ $t('layouts.calendar.color') }}
		</label>
		<v-select
			id="spacing"
			:value="viewOptions.color || '__none__'"
			:options="colorOptions"
			class="select"
			icon="color_lens"
			@input="setOption('color', $event)"
		></v-select>
	</form>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/layout';
import { mapValues, pickBy, identity } from 'lodash';

export default {
	mixins: [mixin],
	data() {
		return {
			sortList: null
		};
	},
	computed: {
		textOptions() {
			var options = mapValues(this.fields, info =>
				info.type == 'string' || info.type == 'integer' ? info.name : null
			);
			return pickBy(options, identity);
		},
		dateOptions() {
			var options = {
				__none__: `(${this.$t('dont_show')})`,
				...mapValues(this.fields, info => (info.type == 'date' ? info.name : null))
			};
			return pickBy(options, identity);
		},
		datetimeOptions() {
			var options = {
				__none__: `(${this.$t('dont_show')})`,
				...mapValues(this.fields, info => (info.type == 'datetime' ? info.name : null))
			};
			return pickBy(options, identity);
		},
		timeOptions() {
			var options = {
				__none__: `(${this.$t('dont_show')})`,
				...mapValues(this.fields, info => (info.type == 'time' ? info.name : null))
			};
			return pickBy(options, identity);
		},
		colorOptions() {
			var options = {
				__none__: `(${this.$t('dont_show')})`,
				...mapValues(this.fields, info => (info.type == 'string' ? info.name : null))
			};
			return pickBy(options, identity);
		}
	},
	methods: {
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

.required::after {
	content: 'required';
	margin: 0 5px;
	padding: 0px 4px 1px;
	font-size: 10px;
	font-weight: 600;
	color: var(--white);
	background-color: var(--warning);
	border-radius: var(--border-radius);
	text-transform: uppercase;
}
</style>
