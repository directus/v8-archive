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
      @change="updateValue(val, $event)" />
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
                ...(this.type === "VARCHAR"
                    ? this.value.split(",")
                    : this.value)
            ];

            if (this.options.wrap && selection.length > 2) {
                selection.pop();
                selection.shift();
            }

            return selection;
        }
    },
    methods: {
        updateValue(val, checked) {
            let selection = [...this.selection];

            if (selection.includes(val)) {
                selection.splice(selection.indexOf(val), 1);
            } else {
                selection.push(val);
            }

            selection.sort();

            selection = selection.join(",");

            if (this.options.wrap && selection.length > 0) {
                selection = `,${selection},`;
            }

            if (this.type === "CSV") {
                selection = selection.split(",");
            }

            this.$emit("input", selection);
        }
    }
};
</script>

<style lang="scss" scoped>
.interface-checkboxes {
    max-width: var(--width-large);
    display: grid;
    grid-gap: 10px;
    grid-template-columns: 25% 25% 25% 25%;
}
</style>
