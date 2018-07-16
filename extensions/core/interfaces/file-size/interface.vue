<template>
  <div v-if="options.formatInput">
    <v-input
      v-model="reducedValue"
      type="number"
      class="interface-file-size"
      :readonly="readonly"
      :placeholder="options.placeholder"
      @input="calculateValue" />
    <v-select
      v-model="units"
      class="interface-file-size-units"
      :disabled="readonly"
      :id="name"
      :options="unitChoices"
      :placeholder="options.placeholder"
      @input="calculateValue" />
  </div>
  <div v-else>
    <v-input
      type="number"
      class="interface-file-size"
      :readonly="readonly"
      :placeholder="options.placeholder"
      :value="value"
      @input="$emit('input', $event)" />
    <span class="interface-file-size-formatted">
      ({{ formatSize(value, true) }})
    </span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import formatSize from "./format-size";

export default {
  mixins: [mixin],
  data() {
    return {
      reducedValue: "1",
      units: "1",
      unitChoices: {
        "1": "B",
        "1000": "kB",
        "1000000": "MB",
        "1000000000": "GB",
        "1000000000000": "TB",
        "1000000000000000": "PB",
        "1000000000000000000": "EB"
      }
    };
  },
  created() {
    if (this.value < 1000) {
      // B
      this.reducedValue = this.value;
      this.units = "1";
    } else if (this.value < 1000000) {
      // kB
      this.reducedValue = this.value / 1000;
      this.units = "1000";
    } else if (this.value < 1000000000) {
      // MB
      this.reducedValue = this.value / 1000000;
      this.units = "1000000";
    } else if (this.value < 1000000000000) {
      // GB
      this.reducedValue = this.value / 1000000000;
      this.units = "1000000000";
    } else if (this.value < 1000000000000000) {
      // TB
      this.reducedValue = this.value / 1000000000000;
      this.units = "1000000000000";
    } else if (this.value < 1000000000000000000) {
      // PB
      this.reducedValue = this.value / 1000000000000000;
      this.units = "1000000000000000";
    } else {
      // EB
      this.reducedValue = this.value / 1000000000000000000;
      this.units = "1000000000000000000";
    }
  },
  methods: {
    formatSize,
    calculateValue() {
      const value = Math.round(this.reducedValue * this.units);
      this.$emit("input", value);
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-file-size-formatted {
  color: var(--light-gray);
  margin-left: 10px;
  font-style: italic;
}
.interface-file-size {
  display: inline-block;
  max-width: var(--width-small);
}
.interface-file-size-units {
  margin: 0;
  display: inline-block;
  min-width: 70px;
  margin-left: 4px;
  max-width: var(--width-x-small);
}
</style>
