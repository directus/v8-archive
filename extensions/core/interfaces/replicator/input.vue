<template>
  <div class="interface-repeater-container">
    <fieldset
      class="group"
      v-for="(value, fieldKey) in localValue"
      :key="fieldKey"
    >
      <ReplicatorField
        v-for="(item, fieldIndex) in value"
        :key="`${fieldKey}-${fieldIndex}`"
        :field="item"
      />
    </fieldset>
    <controls @add-field="addNewField" :config="parsedConfig"></controls>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import Controls from "./controls.vue";
import ReplicatorField from "./field.vue";

/**
 * @typedef {Object} ReplicatorConfig
 * @property {'object'|'text'} type The type of field to use
 * @property {String} title The label for the field
 * @property {?String} default Default value
 * @property {ReplicatorConfig[]} [children] Nested fields
 */

export default {
  name: "ReplicatorInput",
  mixins: [mixin],
  components: { Controls, ReplicatorField },
  computed: {
    /** @returns {ReplicatorConfig[]} */
    parsedConfig() {
      return typeof this.options.items === "string"
        ? JSON.parse(this.options.items)
        : this.options.items;
    },
    localValue() {
      return JSON.parse(this.value);
    }
  },
  methods: {
    /**
     * @param {number}i The index of the item
     */
    addNewField(i) {
      const field = { ...this.parsedConfig[i] };
      const fieldTitle = field.title;
      const newValue = Object.assign({}, { [fieldTitle]: [] }, this.localValue);
      newValue[fieldTitle].push({ values: [], ...field });
      this.updateValue(newValue);
    },
    updateValue(newValue) {
      if (!newValue) return;
      const updatedValue = Object.assign({}, this.localValue, newValue);
      this.$nextTick(() => {
        this.$emit("input", JSON.stringify(updatedValue));
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.v-input {
  width: 100%;
  max-width: var(--width-medium);
}

.group {
  border-bottom: 1px solid var(--gray);
  margin-bottom: 1rem;
}
</style>
