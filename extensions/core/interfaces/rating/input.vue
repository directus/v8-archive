<template>
  <div class="rating">
    <div class="rating-stars">
      <button
        v-for="n in options.max_stars"
        @mouseenter="readonly?'':hovered = n"
        @mouseleave="readonly?'':hovered = null"
        @click="readonly?'':set(n)"
        :class="ratingClass(n)"
        :style="ratingStyle"
        :key="`star_${n}`">
      </button>
    </div>
    <div 
      class="rating-value" 
      v-if="['DECIMAL','DOUBLE'].includes(type)">
      <v-input 
        class="rating-input" 
        type="text"
        :disabled="readonly"
        :maxlength="length"
        v-model="rating"/>
      <span>out of {{options.max_stars}} stars</span>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-rating",
  mixins: [mixin],
  data() {
    return {
      rating: null,
      hovered: null
    };
  },
  created() {
    this.rating = this.value || 0;
  },
  watch: {
    rating(newValue) {
      //? How to stop user from entering value, larger than number of stars
      if (newValue > this.options.max_stars) {
        newValue = this.options.max_stars;
      }
      this.$emit("input", newValue);
    }
  },
  computed: {
    int() {
      if (this.hovered) {
        return this.hovered;
      } else {
        return Math.floor(this.rating);
      }
    },
    frac() {
      if (this.hovered) {
        return 0;
      } else {
        return this.rating - Math.floor(this.rating);
      }
    },
    ratingStyle() {
      return {
        color: "var(--" + this.options.active_color + ")" || false
      };
    }
  },
  methods: {
    set(n) {
      this.hovered = false;
      this.rating = n;
    },
    ratingClass(n) {
      let _class = ["rating-button"];
      if (this.hovered) {
        _class.push("rating-hover");
      }
      if (n <= this.int) {
        _class.push("rating-full");
      }
      if (n == this.int + 1) {
        if (this.frac >= 0.75) {
          _class.push("rating-full");
        } else if (0.75 > this.frac && this.frac >= 0.25) {
          _class.push("rating-half");
        }
      }
      return _class;
    }
  }
};
</script>

<style lang="scss" scoped>
.rating {
  display: flex;
  align-items: center;
}
.rating-stars {
  display: flex;
}
.rating-button {
  width: 36px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: var(--darker-gray);
  //? Used opacity instead of lighter-gray to lighten other colors
  opacity: 0.4;
  &:after {
    content: "star_border";
    font-family: "Material Icons";
    font-size: 36px;
    line-height: 1;
  }
  &.rating-full {
    color: var(--darker-gray);
    opacity: 1;
    &:after {
      content: "star";
    }
  }
  &.rating-half {
    opacity: 1;
    color: var(--darker-gray);
    &:after {
      content: "star_half";
    }
  }
  &.rating-hover {
    opacity: 0.4;
  }
}
.rating-value {
  margin-left: 20px;
  display: flex;
  align-items: center;
  color: var(--gray);
}
.rating-input {
  //? v-input does not accept 'length' so we need to fix width with CSS
  //? Better option would be to pass length to decide width of textbox
  width: 50px;
  margin-right: 10px;
}
</style>
