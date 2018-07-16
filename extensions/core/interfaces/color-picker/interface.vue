<template>
    <div class="interface-color-picker">
      <button
        v-for="color in colors"
        v-tooltip="$helpers.formatTitle(color)"
        :key="color"
        :style="{ backgroundColor: `var(--${color})`}"
        :class="{ active: value === color}"
        @click="$emit('input', color)">
        <template
          v-if="value === color">
          <i class="material-icons">check</i>
        </template>
      </button>
    </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import colors from "./colors.json";

export default {
  mixins: [mixin],
  computed: {
    colors() {
      return colors;
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-color-picker {
  --swatch-size: 20px;
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  height: calc(var(--swatch-size) * 11);
  width: var(--swatch-size);

  button {
    flex-basis: var(--swatch-size);
    width: var(--swatch-size);
    height: var(--swatch-size);
    display: block;
    transform: scale(1);
    transition: transform var(--fast) var(--transition-in);

    &:hover {
      transition: none;
      transform: scale(1.2);
      z-index: +1;
      box-shadow: var(--box-shadow);
    }

    i {
      font-size: 14px;
      color: hsl(0, 0%, 100%);
      filter: saturate(0);
      mix-blend-mode: difference;
    }
    &:nth-last-child(1) {
      flex-grow: 1;
    }
    &:nth-last-child(2) {
      flex-grow: 1;
    }
  }
}
</style>
