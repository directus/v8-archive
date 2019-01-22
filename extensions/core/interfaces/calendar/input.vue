<template>
  <div v-bind:class="{ inactive: readonly }">
    <flat-pickr
      class="form-control"
      :config="configs"
      :value="value"
      @input="$emit('input', $event)"
    >
    </flat-pickr>
  </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import mixin from "../../../mixins/interface";

import "flatpickr/dist/flatpickr.css";
import "./calendar.css";

export default {
  name: "interface-calendar",
  computed: {
    configs() {
      return {
        inline: true,
        minDate: this.options.min,
        maxDate: this.options.max,
        dateFormat: "Y-m-d"
      };
    }
  },
  mixins: [mixin],
  components: {
    flatPickr
  }
};
</script>

<style lang="scss" scoped>
.inactive {
  pointer-events: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
</style>
