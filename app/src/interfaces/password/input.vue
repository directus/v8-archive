<template>
	<div class="interface-password">
		<v-input
			id="first"
			class="password first"
			:type="inputType"
			:placeholder="options.placeholder"
			:value="value"
			:readonly="readonly"
			:icon-right="lockIcon"
			:icon-right-color="iconColor"
			@input="$emit('input', $event)"
		></v-input>
		<!--
    -->
		<v-input
			v-if="false"
			id="second"
			v-model="confirmValue"
			class="password second"
			:type="inputType"
			:placeholder="$t('interfaces.password.confirm_placeholder')"
			:icon-right="confirmIcon"
			:icon-right-color="confirmColor"
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
			confirmValue: this.value || '',
			matches: true
		};
	},
	computed: {
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
		},
		confirmIcon() {
			return this.matches ? 'check' : 'close';
		},
		confirmColor() {
			return this.matches ? 'accent' : 'danger';
		}
	},
	watch: {
		value() {
			this.matches = this.value === this.confirmValue ? true : false;
		},
		confirmValue() {
			this.matches = this.value === this.confirmValue ? true : false;
		}
	}
};
</script>

<style lang="scss" scoped>
.password {
	display: inline-block;
	width: 100%;
	max-width: var(--width-medium);
}
.first {
	margin-right: 20px;
}
.second {
}
@media only screen and (max-width: 660px) {
	.first {
		margin-bottom: 10px;
	}
}
</style>
