<template>
  <v-timeago
    v-if="value && options.showRelative"
    :since="date"
    :auto-update="86400"
    :locale="$i18n.locale"
    class="no-wrap"
  ></v-timeago>
  <div v-else>{{ displayValue }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    date() {
      if (this.value) {
        return new Date(this.value.replace(/-/g, "/"));
      }
      return null;
    },
    displayValue() {
      if (this.value && this.options.localized) {
        return this.$d(this.date, "short");
      }

      return this.value;
    }
  }
};
</script>
