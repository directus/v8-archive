<template>
	<small v-if="parseError" class="notice">
		<v-icon name="warning" />
		<span>
			{{ $t('interfaces.dropdown.options_invalid') }}
			<br />
			{{ parseError }}
		</span>
	</small>
	<v-select
		v-else
		:id="name"
		:value="value"
		:options="choices"
		:placeholder="options.placeholder"
		:icon="options.icon"
		:other="options.allow_other"
		@input="$emit('input', $event)"
	></v-select>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	data() {
		return {
			parseError: null
		};
	},
	computed: {
		choices() {
			let choices = this.options.choices;
			if (!choices) return {};
			if (typeof this.options.choices === 'string') {
				try {
					choices = JSON.parse(this.options.choices);
				} catch (error) {
					this.parseError = error.toString(); // eslint-disable-line
				}
			}
			return choices;
		}
	}
};
</script>

<style lang="scss" scoped>
.v-select {
	margin-top: 0;
	max-width: var(--width-medium);
}

.notice {
	display: flex;
	align-items: center;

	i {
		margin-right: 1rem;
	}
}
</style>
