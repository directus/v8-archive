<template>
  <div class="interface-radio-buttons">
    <v-radio
      v-for="(display, val) in optionValues"
      :id="`${name}-${val}`"
      :name="name"
      :value="val"
      :key="val"
      :modelValue="value"
      :label="display"
      @change="$emit('input', $event)" />
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    name: "interface-radio-buttons",
    mixins: [mixin],
    computed: {
        optionValues() {
            if (typeof this.options.choices === "string") {
                return this.options.choices ? JSON.parse(this.choices) : {};
            }

            return this.options.choices || {};
        }
    }
};
</script>
