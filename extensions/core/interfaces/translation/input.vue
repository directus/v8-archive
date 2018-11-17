<template>
  <div v-if="error || !relation" class="translation error">
    <p><i class="material-icons">warning</i> {{ $t('interfaces-translation-translation_not_setup') }}</p>
  </div>
  <div v-else-if="languages && languages.length === 0" class="translation error">
    <p><i class="material-icons">warning</i> {{ $t('interfaces-translation-translation_no_languages') }}</p>
  </div>
  <div v-else-if="activeLanguage" class="translation">
    <v-simple-select class="language-select" v-model="activeLanguage" :placeholder="$t('interfaces-translation-choose_language')">
      <option v-for="language in languages" :key="language.code" :value="language.code">
        {{ language.name }}
      </option>
    </v-simple-select>

    <hr />

    <v-form
      class="form"
      :key="activeLanguage"
      :fields="relatedFields"
      :values="langValue"
      :collection="relation.collection_many"
      ref="form"
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
      languages: null
    };
  },
  computed: {
    error() {
      if (
        !this.options.languagesCollection ||
        !this.options.translationLanguageField
      )
        return true;
      return false;
    },
    primaryKeyField() {
      return this.$lodash.find(this.fields, { primary_key: true }).field;
    },
    primaryKey() {
      return this.values[this.primaryKeyField];
    },
    relatedFields() {
      if (!this.relation) return null;
      return this.relation.collection_many.fields;
    },
    languageFields() {
      if (!this.options.languagesCollection) return null;
      return this.$store.state.collections[this.options.languagesCollection]
        .fields;
    },
    langValue() {
      if (!this.languages || !this.activeLanguage) return {};
      if (!this.value) return {};

      return this.valuesByLang[this.activeLanguage] || {};
    },
    valuesByLang() {
      if (!this.value) return {};
      return this.$lodash.keyBy(
        this.value,
        this.options.translationLanguageField
      );
    }
  },
  created() {
    this.fetchLanguages();
  },
  methods: {
    fetchLanguages() {
      if (
        !this.options.languagesCollection ||
        !this.options.translationLanguageField
      )
        return null;
      this.$api
        .getItems(this.options.languagesCollection, { limit: -1 })
        .then(res => res.data)
        .then(languages => {
          if (languages.length === 0) return;

          const primaryKeyField = this.options.languagesPrimaryKeyField;
          const nameField = this.options.languagesNameField;

          this.languages = languages.map(language => {
            return {
              code: language[primaryKeyField],
              name: language[nameField]
            };
          });

          this.activeLanguage =
            this.options.defaultLanguage ||
            languages[0][
              this.$lodash.find(this.languageFields, {
                primary_key: true
              }).field
            ];
        });
    },
    stageValue({ field, value }) {
      if (!this.valuesByLang[this.activeLanguage]) {
        const newValue = this.newItem
          ? [
              ...(this.value || []),
              {
                [this.options.translationLanguageField]: this.activeLanguage,
                [field]: value
              }
            ]
          : [
              ...(this.value || []),
              {
                [this.relation.field_many.field]: this.primaryKey,
                [this.options.translationLanguageField]: this.activeLanguage,
                [field]: value
              }
            ];

        return this.$emit("input", newValue);
      }

      return this.$emit(
        "input",
        this.value.map(translation => {
          if (
            translation[this.options.translationLanguageField] ===
            this.activeLanguage
          ) {
            return {
              ...translation,
              [field]: value
            };
          }

          return translation;
        })
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.translation {
  width: 100%;
  padding: var(--page-padding);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);

  &.disabled {
    position: relative;

    .language-select,
    hr,
    .form {
      user-select: none;
      pointer-events: none;
      opacity: 0.2;
    }

    p {
      top: 0;
      left: 0;
      position: absolute;
      width: 100%;
      height: 100%;
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }
}

hr {
  margin: 20px 0;
  border: 0;
  border-bottom: 1px dashed var(--lighter-gray);
}
</style>
