<template>
  <div v-if="value" class="gray style-3">
    <v-timeago
      v-if="options.showRelative"
      :since="date"
      :auto-update="60"
      :locale="$i18n.locale"
      v-tooltip="displayValue"
      class="no-wrap"
    ></v-timeago>
    <div v-else>{{displayValue}}</div>
  </div>
  <div v-else-if="newItem" class="gray style-3">{{ $t("interfaces-datetime-created-now") }}</div>
  <div v-else class="gray style-3">{{ $t("interfaces-datetime-created-unknown") }}</div>
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

<style lang="scss" scoped>
.gray {
  color: var(--light-gray);
  text-transform: capitalize;
}
</style>
