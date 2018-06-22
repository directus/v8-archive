<template>
  <v-timeago
    v-if="options.showRelative"
    :since="date"
    :auto-update="options.includeSeconds ? 1 : 60"
    :locale="$i18n.locale" />
  <span v-else class="no-wrap">{{displayValue}}</span>
</template>

<script>
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
  computed: {
    displayValue() {
      if (this.value && this.options.display24HourClock === false) {
        const timeParts = this.value.split(':').map(str => Number(str));
        let hours = timeParts[0]
        const minutes = timeParts[1];
        const seconds = timeParts[2];

        let suffix = 'AM';

        if (hours >= 12) {
          suffix = 'PM';
        }

        hours = (hours > 12) ? hours - 12 : hours;
        hours = (hours == '00') ? 12 : hours;

        if (seconds) {
          return `${hours}:${minutes}:${seconds} ${suffix}`;
        }

        return `${hours}:${minutes} ${suffix}`;
      }

      return this.value;
    },
  },
};
</script>
