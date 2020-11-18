<template>
	<v-icon v-tooltip="tooltipCopy" :class="{ empty }" name="code" />
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyCode',
	mixins: [mixin],
	computed: {
		lineCount() {
			if (!this.value) return 0;

			let value = this.value;

			if (typeof this.value === 'object') {
				value = JSON.stringify(this.value);
			}

			return value.split(/\r\n|\r|\n/).length;
		},
		availableTypes() {
			return {
				'text/javascript': 'JavaScript',
				'application/json': 'JSON',
				'text/x-vue': 'Vue',
				'application/x-httpd-php': 'PHP'
			};
		},
		language() {
			return this.availableTypes[this.options.language];
		},
		tooltipCopy() {
			return this.$tc('interfaces.code.loc', this.lineCount, {
				count: this.lineCount,
				lang: this.language
			});
		},
		empty() {
			return this.lineCount === 0;
		}
	}
};
</script>

<style lang="scss" scoped>
i.material-icons {
	cursor: help;

	&.empty {
		color: var(--empty-value);
	}
}
</style>
