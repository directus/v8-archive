<template>
	<v-simple-select
		:placeholder="$t('interfaces.language.select_language')"
		:value="value"
		:disabled="readonly"
		@input="$emit('input', $event)"
	>
		<option v-for="(name, code) in languages" :key="code" :value="code">
			{{ name }}
		</option>
	</v-simple-select>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import allLanguages from './languages.json';

export default {
	mixins: [mixin],
	computed: {
		languages() {
			if (this.options.limit) {
				return this.$i18n.availableLanguages;
			}

			return allLanguages;
		}
	}
};
</script>

<style lang="scss" scoped></style>
