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
import mixin from '../../../mixins/interface';
import dateFormat from 'dateformat';

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
        return new Date(this.value.replace(/-/g, '/'));
      }
      return null;
    },
    displayValue() {
      if (this.value && this.options.localized) {
        return this.$d(this.date, 'short');
      } else {
        return dateFormat(this.date, this.options.formatting);
      }  
    },
  },
};
</script>
