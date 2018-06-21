<template>
  <div
    v-if="currentStatus && currentStatus.listing_badge"
    :class="['badge', 'no-wrap', { simple: options.simpleBadge }]"
    :style="style"
    v-tooltip="options.simpleBadge ? currentStatus.name : false">{{ $t(currentStatus.name) }}</div>
</template>

<script>
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
  computed: {
    currentStatus() {
      return this.options.statusMapping[this.value];
    },
    style() {
      return {
        backgroundColor: `var(--${this.currentStatus.background_color})`,
        color: `var(--${this.currentStatus.text_color})`
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.badge {
  border-radius: var(--border-radius);
  padding: 5px 10px;
  display: inline-block;
  cursor: default;
  max-width: 120px;

}
.simple {
  border-radius: 100%;
  overflow: hidden;
  font-size: 0;
  padding: 6px;
  vertical-align: middle;
}
</style>
