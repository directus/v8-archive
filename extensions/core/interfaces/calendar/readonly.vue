<template>
  <v-timeago
    v-if="value && showRelative"
    :since="date"
    :auto-update="86400"
    :locale="$i18n.locale"
    class="no-wrap"
  ></v-timeago>
  <div v-else>{{displayValue}}</div>
</template>

<script>
import dateFormat from "dateformat";
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    showRelative() {
      if (this.options.formatting == "" || this.options.formatting == null) {
        return true;
      }
      return false;
    },
    date() {
      if (this.value) {
        return new Date(this.value.replace(/-/g, "/"));
      }
      return null;
    },
    displayValue() {
      return dateFormat(this.date, this.options.formatting);
    }
  }
};
</script>
