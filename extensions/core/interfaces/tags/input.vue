<template>
  <div class="interface-tags">
    <v-input
      class="input"
      type="text"
      :placeholder="$t('interfaces-tags-placeholder_text')"
      :icon-left="options.iconLeft"
      :icon-right="options.iconRight"
      :icon-right-color="null"
      @keydown="onInput"
    ></v-input>
    <div class="buttons">
      <button
        v-for="(value, index) in valueArray"
        :key="index"
        @click.prevent="removeTag(index)"
      >{{ value }}</button>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    valueArray() {
      if (Boolean(this.value) === false) {
        return [];
      }

      /**
       * In some cases(i.e. Directus Settings),
       * You'll get 'this.type' as a normal string from API
       * even if it's type is set to an array.
       * So you can not rely on it so explicitly checking the type of 'this.value'
       */
      let array;
      if (Array.isArray(this.value)) {
        array = this.value;
      } else {
        /**
         * When the "Wrap" option is on but the value does not contain
         * the leading and/or trailing comma.
         * We've to check values before dropping first and last values
         */
        let _value = this.value.trim();
        if (_value.charAt(0) === ",") _value = _value.slice(1);
        if (_value.charAt(_value.length - 1) === ",")
          _value = _value.slice(0, -1);
        array = _value.split(",");
      }

      return array;
    }
  },
  methods: {
    onInput(event) {
      if ((event.target.value && event.key === "Enter") || event.key === ",") {
        event.preventDefault();
        this.addTag(event.target.value);
        event.target.value = "";
      }
    },
    addTag(tag) {
      let tags = [...this.valueArray];

      tag = tag.trim();

      if (this.options.lowercase) {
        tag = tag.toLowerCase();
      }

      if (this.options.sanitize) {
        tag = tag.replace(/([^a-z0-9]+)/gi, "-").replace(/^-|-$/g, "");
      }

      if (tag.length > 0) tags.push(tag);

      if (this.options.alphabetize) {
        tags.sort();
      }

      // Remove any duplicates
      tags = [...new Set(tags)];

      this.emitValue(tags);
    },
    removeTag(index) {
      const tags = this.valueArray.splice(0);
      tags.splice(index, 1);

      this.emitValue(tags);
    },
    emitValue(tags) {
      let value = tags.join(",");

      if (value && this.options.wrap) {
        value = `,${value},`;
      }

      if (this.type === "array") {
        this.$emit("input", value.split(","));
      } else {
        this.$emit("input", value);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-tags {
  max-width: var(--width-medium);
}

.buttons {
  display: flex;
  flex-wrap: wrap;
  padding: 5px 0;

  button {
    transition: var(--fast) var(--transition);
    margin: 2px;
    padding: 2px 4px 3px;
    background-color: var(--gray);
    color: var(--white);
    border-radius: var(--border-radius);

    &:hover,
    .user-is-tabbing &:focus {
      background-color: var(--danger);
    }
  }
}
</style>
