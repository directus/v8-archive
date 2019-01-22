<template>
  <v-timeago
    v-if="value && options.showRelative"
    :since="date"
    :auto-update="60"
    :locale="$i18n.locale"
    v-tooltip="displayValue"
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
      if (!this.value) return null;

      let date = this.value;

      if (this.options.utc) {
        date = this.value.includes("T")
          ? this.value.substring(0, 16)
          : this.toDatetimeLocal(new Date(this.value.replace(" ", "T") + "Z"));
      }

      return new Date(date);
    },
    displayValue() {
      if (!this.date) return;
      return this.$d(this.date, "long");
    }
  },

  methods: {
    toDatetimeLocal(date) {
      const yyyy = date.getFullYear();
      const mm = this.ten(date.getMonth() + 1);
      const dd = this.ten(date.getDate());
      const hh = this.ten(date.getHours());
      const ii = this.ten(date.getMinutes());
      const ss = this.ten(date.getSeconds());
      return `${yyyy}-${mm}-${dd}T${hh}:${ii}:${ss}`;
    },
    ten(num) {
      return String(num).padStart(2, 0);
    }
  }
};
</script>
