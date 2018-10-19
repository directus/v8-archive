<template>
  <v-timeago
    v-if="value && options.showRelative"
    :since="date"
    :auto-update="60"
    :locale="$i18n.locale"
    v-tooltip="displayValue"
    class="no-wrap"
  ></v-timeago>
  <div v-else>{{displayValue}}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    formattedValue() {
      return this.value && this.value.substring(0, 16); // yyyy-mm-ddThh:ss
    },
    date() {
      if (!this.formattedValue) return null;
      return new Date(this.formattedValue);
    },
    displayValue() {
      if (!this.date) return;
      return this.$d(this.date, "long");
    }
  }
};
</script>
