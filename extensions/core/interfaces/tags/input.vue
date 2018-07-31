<template>
  <div class="interface-tags">
    <v-input
      class="input"
      type="text"
      :placeholder="$t('interfaces-tags-placeholder_text')"
      :icon-left="options.iconLeft"
      :icon-right="options.iconRight"
      @keydown="onInput" />
    <div class="buttons">
      <button
        v-for="(value, index) in valueArray"
        :key="index"
        @click.prevent="removeTag(index)"
        >{{value}}</button>
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

      if (typeof this.value === "string") {
        const array = this.value.split(",");

        if (this.options.wrap) {
          array.pop();
          array.shift();
        }

        return array;
      }

      return this.value;
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

      this.$emit("input", value);
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-tags {
  max-width: var(--width-normal);
}

.buttons {
  display: flex;
  flex-wrap: wrap;
  padding: 5px 0;

  button {
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
