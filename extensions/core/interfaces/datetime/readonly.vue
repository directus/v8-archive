<template>
  <v-timeago
    v-if="value && options.showRelative"
    :since="since"
    :auto-update="60"
    :locale="$i18n.locale"
  ></v-timeago>
  <div v-else>{{displayValue}}</div>
</template>

<script>
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
  computed: {
    date() {
      if (this.value) {
        return this.$helpers.date.sqlToDate(this.value);
      }
      return null;
    },
    since() {
      if (this.value) {
        const dateISOString = this.date.toISOString();
        return dateISOString.substring(0, dateISOString.length - 1);
      }
      return null;
    },
    displayValue() {
      if (this.value && this.options.localized) {
        return this.$d(this.date, 'long');
      }

      return this.value;
    },
  },
};
</script>
