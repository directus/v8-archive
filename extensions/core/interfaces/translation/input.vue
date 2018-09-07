<template>
  <div v-if="error" class="translation error">
    <p><i class="material-icons">warning</i> {{ $t('interfaces-translation-translation_not_setup') }}</p>
  </div>
  <div v-else-if="activeLanguage" class="translation">
    <v-simple-select class="language-select" v-model="activeLanguage">
      <option v-for="language in languages" :key="language.code" :value="language.code">
        {{ language.name }}
      </option>
    </v-simple-select>

    <hr />

    <v-form
      :key="activeLanguage"
      :fields="relatedFields"
      :values="langValue"
      :collection="relation.collection_many"
      ref="form"
      @unstage-value="unstageValue"
      @stage-value="stageValue" />
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      activeLanguage: null,
      languages: []
    };
  },
  computed: {
    error() {
      if (!this.options.languagesCollection || !this.options.languagePrimaryKeyField) return true;
      return false;
    },
    relatedFields() {
      if (!this.relation) return null;
      return this.relation.collection_many.fields;
    },
    langValue() {
      if (!this.languages || !this.activeLanguage) return {};
      if (!this.value) return {};

      return this.valuesByLang[this.activeLanguage] || {};
    },
    valuesByLang() {
      if (!this.value) return {};
      return this.$lodash.keyBy(this.value, this.relation.field_many.field);
    }
  },
  created() {
    this.fetchLanguages();
  },
  methods: {
    fetchLanguages() {
      if (!this.options.languagesCollection || !this.options.languagePrimaryKeyField) return null;
      this.$api.getItems(this.options.languagesCollection, { limit: -1 })
        .then(res => res.data)
        .then(languages => {
          this.languages = languages;
          this.activeLanguage = languages[languages.length - 1][this.options.languagePrimaryKeyField];
        });
    },
    stageValue({ field, value }) {
      if (!this.valuesByLang[this.activeLanguage]) {
        return this.$emit("input", [
          ...(this.value || []),
          {
            [this.relation.field_many.field]: this.activeLanguage,
            [field]: value
          }
        ]);
      }

      return this.$emit("input", this.value.map(translation => {
        if (translation[this.relation.field_many.field] === this.activeLanguage) {
          return {
            ...translation,
            [field]: value
          };
        }

        return translation;
      }));
    },
    unstageValue() {

    }
  }
}
</script>

<style lang="scss" scoped>
.translation {
  width: 100%;
  padding: var(--page-padding);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
}

hr {
  margin: 20px 0;
  border: 0;
  border-bottom: 1px dashed var(--lighter-gray);
}
</style>
