<template>
	<v-input
		:id="name"
		type="text"
		class="interface-datetime"
		:name="name"
		:min="options.min"
		:max="options.max"
		:readonly="readonly"
		:value="formattedValue"
		:icon-left="options.iconLeft"
		:icon-right="options.iconRight"
		:placeholder="placeholder"
		:mask="mask"
		@input="updateValue"
	></v-input>
</template>

<script>
/* eslint-disable vue/return-in-computed-property */

import mixin from '@directus/extension-toolkit/mixins/interface';
import format from 'date-fns/format';
import parse from 'date-fns/parse';

export default {
	mixins: [mixin],
	computed: {
		formattedValue() {
			if (!this.value) return null;

			if (this.value.includes('+')) {
				return format(new Date(this.value), this.format);
			}

			return format(parse(this.value, 'yyyy-MM-dd HH:mm:ss', new Date()), this.format);
		},
		placeholder() {
			switch (this.options.format) {
				case 'dmy':
					return 'dd/mm/yyyy hh:mm:ss';
				case 'mdy':
					return 'mm/dd/yyyy hh:mm:ss';
				case 'ymd':
					return 'yyyy-mm-dd hh:mm:ss';
			}
		},
		mask() {
			switch (this.options.format) {
				case 'dmy':
				case 'mdy':
					return '##/##/#### ##:##:##';
				case 'ymd':
					return '####-##-## ##:##:##';
			}
		},
		format() {
			switch (this.options.format) {
				case 'dmy':
					return 'dd/MM/yyyy HH:mm:ss';
				case 'mdy':
					return 'MM/dd/yyyy HH:mm:ss';
				case 'ymd':
					return 'yyyy-MM-dd HH:mm:ss';
			}
		}
	},
	created() {
		if (this.options.defaultToCurrentDatetime && !this.value) {
			this.$emit('input', format(new Date(), 'yyyy-MM-dd HH:mm:ss'));
		}
	},
	methods: {
		updateValue(value) {
			if (!value || value.length === 0) return this.$emit('input', null);

			if (value.length === 19) {
				const dbValue = format(
					parse(value, this.format, new Date()),
					'yyyy-MM-dd HH:mm:ss'
				);
				if (dbValue !== 'Invalid Date') return this.$emit('input', dbValue);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-datetime {
	max-width: var(--width-medium);
}
</style>
