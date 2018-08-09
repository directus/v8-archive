<template>
  <form @submit.prevent>
    <label for="title" class="style-4">{{ $t('layouts-cards-title') }}</label>
    <v-select
      id="title"
      :value="viewOptions.title"
      :options="fieldOptions"
      :placeholder="$t('select_field')"
      @input="setOption('title', $event === '__none__' ? null : $event)"></v-select>

    <label for="subtitle" class="style-4">{{ $t('layouts-cards-subtitle') }}</label>
    <v-select
      id="subtitle"
      :value="viewOptions.subtitle"
      :options="fieldOptions"
      :placeholder="$t('select_field')"
      @input="setOption('subtitle', $event === '__none__' ? null : $event)"></v-select>

    <label for="src" class="style-4">{{ $t('layouts-cards-src') }}</label>
    <v-select
      id="src"
      :value="viewOptions.src"
      :options="fileOptions"
      :placeholder="$t('select_field')"
      @input="setOption('src', $event === '__none__' ? null : $event)"></v-select>

    <label for="content" class="style-4">{{ $t('layouts-cards-content') }}</label>
    <v-select
      id="content"
      :value="viewOptions.content"
      :options="fieldOptions"
      :placeholder="$t('select_field')"
      @input="setOption('content', $event === '__none__' ? null : $event)"></v-select>

    <label for="icon" class="style-4">Fallback Icon</label>
    <v-input
      :value="viewOptions.icon || 'photo'"
      @input="setOption('icon', $event)"></v-input>
  </form>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  mixins: [mixin],
  computed: {
    fieldOptions() {
      return {
        __none__: `(${this.$t("dont_show")})`,
        ...this.$lodash.mapValues(this.fields, info => info.name)
      };
    },
    fileOptions() {
      const fileTypeFields = this.$lodash.filter(
        this.fields,
        info => info.type.toLowerCase() === "file"
      );
      const fields = this.$lodash.keyBy(fileTypeFields, "field");
      const options = {
        __none__: `(${this.$t("dont_show")})`,
        ...this.$lodash.mapValues(fields, info => info.name)
      };

      // Check if one of the fields is `data`. If that's the case, make sure that this
      //   field is for the directus_files collection and it's an ALIAS type
      //
      // This is a hardcoded addition to make sure that directus_files can be used in the cards view preview
      if ("data" in this.fields) {
        const field = this.fields.data;

        if (
          field.type.toLowerCase() === "alias" &&
          field.collection === "directus_files"
        ) {
          options.data = this.$t("file");
        }
      }

      return options;
    }
  },
  methods: {
    setOption(field, value) {
      this.$emit("options", {
        ...this.viewOptions,
        [field]: value
      });
    }
  }
};
</script>

<style lang="scss" scoped>
label {
  margin-bottom: 5px;
  &:not(:first-of-type) {
    margin-top: 20px;
  }
}
</style>
