<template>
  <div class="interface-checkboxes">
    <v-checkbox
      v-for="(name, val) in options.choices"
      :id="name"
      :key="name"
      :value="val"
      :disabled="readonly"
      :label="name"
      :checked="selection.includes(val)"
      @change="updateValue(val, $event)"></v-checkbox>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-checkboxes",
  mixins: [mixin],
  computed: {
    selection() {
      if (this.value == null) return [];

      const selection = [
        ...(this.type === "string" ? this.value.split(",") : this.value)
      ];

      if (this.options.wrap && selection.length > 2) {
        selection.pop();
        selection.shift();
      }

      return selection;
    }
  },
  methods: {
    updateValue(val) {
      let selection = [...this.selection];

      if (selection.includes(val)) {
        selection.splice(selection.indexOf(val), 1);
      } else {
        selection.push(val);
      }

      selection = selection.join(",");

      if (this.options.wrap && selection.length > 0) {
        selection = `,${selection},`;
      }

      if (this.type === "array") {
        selection = selection.split(",");
      }

      this.$emit("input", selection);
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-checkboxes {
  max-width: var(--width-x-large);
  display: grid;
  grid-gap: 20px;
  grid-template-columns: repeat(4, 1fr);
}
</style>
