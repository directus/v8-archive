<template>
  <div v-if="error" class="translation error">
    <p><i class="material-icons">warning</i> {{ $t('interfaces-translation-translation_not_setup') }}</p>
  </div>
  <div v-else-if="activeLanguage" class="translation" :class="{ disabled: newItem }">
    <v-simple-select :disabled="newItem" class="language-select" v-model="activeLanguage">
      <option v-for="language in languages" :key="language.code" :value="language.code">
        {{ language.name }}
      </option>
    </v-simple-select>

    <hr />

    <v-form
      class="form"
      :readonly="newItem"
      :key="activeLanguage"
      :fields="relatedFields"
      :values="langValue"
      :collection="relation.collection_many"
      ref="form"
      @stage-value="stageValue" />

    <p v-if="newItem"><i class="material-icons">help_outline</i> {{ $t('interfaces-translation-save_first') }}</p>
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
      return this.$store.state.collections[this.options.languagesCollection].fields;
    },
    langValue() {
      if (!this.languages || !this.activeLanguage) return {};
      if (!this.value) return {};

      return this.valuesByLang[this.activeLanguage] || {};
    },
    valuesByLang() {
      if (!this.value) return {};
      return this.$lodash.keyBy(this.value, this.options.languagePrimaryKeyField);
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
          this.activeLanguage = languages[languages.length - 1][this.$lodash.find(this.languageFields, { primary_key: true }).field];
        });
    },
    stageValue({ field, value }) {
      if (!this.valuesByLang[this.activeLanguage]) {
        return this.$emit("input", [
          ...(this.value || []),
          {
            [this.relation.field_many.field]: this.primaryKey,
            [this.options.languagePrimaryKeyField]: this.activeLanguage,
            [field]: value
          }
        ]);
      }

      return this.$emit("input", this.value.map(translation => {
        if (translation[this.options.languagePrimaryKeyField] === this.activeLanguage) {
          return {
            ...translation,
            [field]: value
          };
        }

        return translation;
      }));
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
