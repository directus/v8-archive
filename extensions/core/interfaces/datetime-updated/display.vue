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
    date() {
      if (!this.value) return null;
      return new Date(this.value.replace(" ", "T") + "Z");
    },
    displayValue() {
      if (!this.date) return;
      return this.$d(this.date, "long") + " GMT";
    }
  }
};
</script>
