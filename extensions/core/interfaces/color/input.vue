<template>
  <div class="interface-color">
    <div class="input" v-if="
      !options.paletteOnly &&
      options.input === 'hex' &&
      readonly === false
    ">
      <v-input
        v-if="options.allowAlpha"
        type="text"
        placeholder="#3498dbee"
        pattern="[#0-9a-fA-F]"
        iconLeft="palette"
        :maxlength="9"
        v-model="rawValue"></v-input>
      <v-input
        v-else
        type="text"
        placeholder="#3498db"
        pattern="[#0-9a-fA-F]"
        iconLeft="palette"
        :maxlength="7"
        v-model="rawValue"></v-input>
    </div>
    <div class="sliders" v-else-if="
      !options.paletteOnly &&
      options.input === 'rgb' &&
      readonly === false
    ">
      <label class="slider-label">R</label>
      <v-slider
        :min="0"
        :max="256"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[0]"
      ></v-slider><br>
      <label class="slider-label">G</label>
      <v-slider
        :min="0"
        :max="256"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[1]"
      ></v-slider><br>
      <label class="slider-label">B</label>
      <v-slider
        :min="0"
        :max="256"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[2]"
      ></v-slider><br>
      <label class="slider-label" v-if="options.allowAlpha">A</label>
      <v-slider
        v-if="options.allowAlpha"
        :min="0"
        :max="1"
        :step="0.01"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[3]"
      ></v-slider>
    </div>
    <div class="sliders" v-else-if="
      !options.paletteOnly &&
      options.input === 'hsl' &&
      readonly === false
    ">
      <label class="slider-label">H</label>
      <v-slider
        :min="0"
        :max="360"
        class="slider"
        :alwaysShowOutput=true
        v-model="rawValue[0]"
      ></v-slider><br>
      <label class="slider-label">S</label>
      <v-slider
        :min="0"
        :max="100"
        class="slider"
        :alwaysShowOutput=true
        v-model="rawValue[1]"
      ></v-slider><br>
      <label class="slider-label">L</label>
      <v-slider
        :min="0"
        :max="100"
        class="slider"
        :alwaysShowOutput=true
        v-model="rawValue[2]"
      ></v-slider><br>
      <label class="slider-label" v-if="options.allowAlpha">A</label>
      <v-slider
        v-if="options.allowAlpha"
        :min="0"
        :max="1"
        :step="0.01"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[3]"
      ></v-slider>
    </div>
    <div class="sliders" v-else-if="
      !options.paletteOnly &&
      options.input === 'cmyk' &&
      readonly === false
    ">
      <label class="slider-label">C</label>
      <v-slider
        :min="0"
        :max="100"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[0]"
      ></v-slider><br>
      <label class="slider-label">M</label>
      <v-slider
        :min="0"
        :max="100"
        class="slider"
        :alwaysShowOutput=true
        v-model="rawValue[1]"
      ></v-slider><br>
      <label class="slider-label">Y</label>
      <v-slider
        :min="0"
        :max="100"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[2]"
      ></v-slider><br>
      <label class="slider-label">K</label>
      <v-slider
        :min="0"
        :max="100"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[3]"
      ></v-slider><br>
      <label class="slider-label" v-if="options.allowAlpha">A</label>
      <v-slider
        v-if="options.allowAlpha"
        :min="0"
        :max="1"
        :step="0.01"
        :alwaysShowOutput=true
        class="slider"
        v-model="rawValue[4]"
      ></v-slider>
    </div>
    <div
      class="swatch"
      :style="`background-color: ${color ? color.hex() : 'transparent'}`"><i class="material-icons">check</i></div>
    <button
      v-if="readonly === false"
      v-for="color in palette"
      :key="color"
      :style="{ borderColor: color, color: color, backgroundColor: color }"
      @click="setRawValue(color)"><i class="material-icons">colorize</i></button>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import Color from "color";

export default {
  name: "interface-color",
  mixins: [mixin],
  data() {
    return {
      rawValue: null
    };
  },
  computed: {
    color() {
      try {
        if (this.options.input === "hex") {
          return Color(this.rawValue);
        }
        return Color[this.options.input](this.rawValue);
      } catch (err) {
        return null;
      }
    },
    palette() {
      if (this.options.palette) {
        const rawPalette = Array.isArray(this.options.palette)
          ? this.options.palette
          : this.options.palette.split(",");
        return rawPalette.map(val => Color(val));
      }
    }
  },
  created() {
    this.setDefault();
  },
  watch: {
    rawValue() {
      if (this.color === null) {
        return this.$emit("input", null);
      }

      let value;

      if (this.options.output === "hex") {
        value = this.color.hex();
      } else {
        value = this.color[this.options.output]().array();
        value = value.map((num, index) => {
          if (index === value.length - 1) {
            return Math.round(num * 100) / 100;
          }

          return Math.round(num);
        });
      }

      this.$emit("input", value);
    },
    options: {
      deep: true,
      handler() {
        this.setDefault();
      }
    }
  },
  methods: {
    setDefault() {
      let savedColor = Color(this.value || "#263238");
      this.setRawValue(savedColor);
    },
    setRawValue(color) {
      if (this.options.input === "hex") {
        return (this.rawValue = color.hex());
      }

      return (this.rawValue = color[this.options.input]()
        .array()
        .map(val => {
          return Math.round(val);
        }));
    }
  }
};
</script>

<style scoped lang="scss">
.interface-color {
  max-width: var(--width-large);
}
.input {
  max-width: var(--width-small);
  display: inline-block;
  margin-right: 8px;
  vertical-align: middle;
}

.sliders {
  max-width: 200px;
  display: inline-block;
  margin-right: 36px;
  vertical-align: middle;
  .slider-label {
    display: inline-block;
    color: var(--light-gray);
    width: 14px;
    vertical-align: text-bottom;
  }
  .slider {
    display: inline-block;
    margin-bottom: 8px;
  }
}

.swatch {
  transition: var(--fast) var(--transition);
  display: inline-block;
  width: 40px;
  height: 40px;
  border-radius: 100%;
  vertical-align: middle;
  margin-right: 8px;
  color: var(--white);
  text-align: center;
  i {
    line-height: 40px;
  }
}

button {
  transition: var(--fast) var(--transition);
  position: relative;
  display: inline-block;
  width: 40px;
  height: 40px;
  border-radius: 100%;
  border: 2px solid var(--gray);
  // background-color: var(--white);
  margin-right: 8px;
  margin-bottom: 8px;
  &:first-of-type {
    margin-left: 16px;
    &::before {
      content: "";
      position: absolute;
      top: 0;
      bottom: 0;
      left: -16px;
      border-left: 1px solid var(--lighter-gray);
    }
  }
  &:not(:hover) {
    background-color: var(--white) !important;
  }
  &:hover {
    transition: none;
    color: var(--white) !important;
  }
}
</style>
