<template>
  <div class="readonly-checkboxes no-wrap">{{displayValue}}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-checkboxes",
  mixins: [mixin],
  computed: {
    selection() {
      if (this.value == null) return [];

      const selection =
        this.type === "VARCHAR" ? this.value.split(",") : this.value;

      if (this.options.wrap) {
        selection.pop();
        selection.shift();
      }

      return selection;
    },
    displayValue() {
      if (this.options.formatting) {
        return this.selection.map(val => this.options.choices[val]).join(", ");
      }

      return this.selection.join(", ");
    }
  }
};
</script>
