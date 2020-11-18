<template>
	<div class="interface-encrypted">
		<v-input
			v-model="newValue"
			:placeholder="placeholder"
			:type="inputType"
			:icon-right="lockIcon"
			:icon-right-color="iconColor"
		></v-input>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	data() {
		return {
			originalValue: this.value || '',
			newValue: ''
		};
	},
	computed: {
		placeholder() {
			if (!this.originalValue) {
				return this.options.placeholder;
			} else if (this.options.showHash) {
				return this.originalValue;
			} else {
				return this.$t('interfaces.hashed.secured');
			}
		},
		valueChanged() {
			return this.value !== this.originalValue;
		},
		inputType() {
			return this.options.hide ? 'password' : 'text';
		},
		lockIcon() {
			return this.valueChanged ? 'lock_open' : 'lock_outline';
		},
		iconColor() {
			return this.valueChanged ? 'warning' : 'accent';
		}
	},
	watch: {
		newValue(val) {
			this.$emit('input', val);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-input {
	width: 100%;
	max-width: var(--width-medium);
}
</style>
