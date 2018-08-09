<template>
  <v-timeago
    v-if="value && options.showRelative"
    :since="since"
    :auto-update="60"
    :locale="$i18n.locale"
    v-tooltip="ISO"
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
      if (this.value) {
        return new Date(this.value);
      }
      return null;
    },
    since() {
      if (this.value) {
        return this.date;
      }
      return null;
    },
    displayValue() {
      if (this.value && this.options.localized) {
        return this.$d(this.date, "long");
      }

      return this.value;
    },
    ISO() {
      if (!this.value) return;
      const ISOString = this.date.toISOString();
      return ISOString.substring(0, ISOString.length - 1);
    }
  }
};
</script>
