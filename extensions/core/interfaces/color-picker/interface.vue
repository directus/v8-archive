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

// var R, G, B, C, L;
// C = [ R/255, G/255, B/255 ];

// for ( var i = 0; i < C.length; ++i ) {
//   if ( C[i] <= 0.03928 ) {
//     C[i] = C[i] / 12.92
//   } else {
//     C[i] = Math.pow( ( C[i] + 0.055 ) / 1.055, 2.4);
//   }
// }
// L = 0.2126 * C[0] + 0.7152 * C[1] + 0.0722 * C[2];

// if ( L > 0.179 ) {
//   this.invert = "#000000";
// } else {
//   this.invert = "#FFFFFF";
// }

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
      transform: scale(1.4);
      z-index: +1;
      box-shadow: var(--box-shadow);
    }

    i {
      font-size: 18px;
      margin-top: -3px;
      color: hsl(0, 0%, 100%);
      filter: saturate(0);
      mix-blend-mode: difference;
    }
    &:nth-last-child(1) {
      flex-grow: 1;
      &:hover {
        transform: scale(1.1);
      }
    }
    &:nth-last-child(2) {
      flex-grow: 1;
      &:hover {
        transform: scale(1.1);
      }
    }
  }
}
</style>
