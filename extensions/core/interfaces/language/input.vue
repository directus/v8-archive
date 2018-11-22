<template>
  <v-simple-select
    :placeholder="$t('interfaces-language-select_language')"
    :value="value"
    @input="$emit('input', $event)"
  >
    <option
      v-for="name, code in languages"
      :value="code"
    >
      {{ name }}
    </option>
  </v-simple-select>
</template>

<script>
import mixin from "../../../mixins/interface";
import allLanguages from "./languages.json";

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

<style lang="scss" scoped>

</style>
