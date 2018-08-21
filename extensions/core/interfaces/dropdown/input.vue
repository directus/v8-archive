<template>
  <v-select
    :class="width"
    :value="value"
    :disabled="readonly"
    :id="name"
    :options="choices"
    :placeholder="options.placeholder"
    :icon="options.icon"
    @input="$emit('input', $event)"></v-select>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    choices() {
      let choices = this.options.choices;

      if (!choices) return {};

      if (typeof this.options.choices === "string") {
        choices = JSON.parse(this.options.choices);
      }

      return choices;
    },
    width() {
      if (!this.choices) return "medium";

      let longestValue = "";
      Object.values(this.choices).forEach(choice => {
        if (choice.length > longestValue.length) {
          longestValue = choice;
        }
      });

      const length = longestValue.length;

      if (length <= 25) return "small";
      else return "medium";
    }
  }
};
</script>

<style lang="scss" scoped>
.v-select {
  margin-top: 0;
  max-width: var(--width-normal);
}

.small {
  max-width: var(--width-small);
}

.medium {
  max-width: var(--width-normal);
}
</style>
