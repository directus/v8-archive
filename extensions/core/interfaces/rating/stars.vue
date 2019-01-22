<template>
  <div
    :class="[
      'rating-stars',
      { 'rating-readonly': readonly },
      { 'rating-small': small }
    ]"
  >
    <button
      v-for="n in options.max_stars"
      @mouseenter="readonly ? '' : (hovered = n)"
      @mouseleave="readonly ? '' : (hovered = null)"
      @click="readonly ? '' : set(n)"
      :class="ratingClass(n)"
      :style="ratingStyle(n)"
      :key="`star_${n}`"
    ></button>
  </div>
</template>

<script>
export default {
  props: {
    readonly: {
      type: Boolean,
      default: false
    },
    small: {
      type: Boolean,
      default: false
    },
    rating: {
      default: 0
    },
    options: {}
  },
  data() {
    return {
      hovered: null
    };
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
    }
  },
  methods: {
    set(n) {
      this.hovered = false;
      this.$emit("update:rating", n);
    },
    starType(n) {
      if (n <= this.int) {
        return "full";
      } else if (n == this.int + 1) {
        if (this.frac >= 0.75) {
          return "full";
        } else if (0.75 > this.frac && this.frac >= 0.25) {
          return "half";
        } else {
          return "empty";
        }
      } else {
        return "empty";
      }
    },
    ratingStyle(n) {
      let _style = {};
      let _starType = this.starType(n);
      if (_starType != "empty") {
        if (this.hovered) {
          _style.color = `var(--dark-gray)`;
        } else {
          _style.color = `var(--${this.options.active_color})`;
        }
      }
      return _style;
    },
    ratingClass(n) {
      let _class = ["rating-button"];
      let _starType = this.starType(n);
      _class.push(`rating-${_starType}`);
      if (this.hovered) {
        _class.push("rating-hover");
      }
      return _class;
    }
  }
};
</script>

<style lang="scss" scoped>
.rating-stars {
  display: flex;
}
.rating-readonly {
  .rating-button {
    cursor: initial;
  }
}
.rating-small {
  .rating-button {
    width: 18px;
    height: 20px;
    &:after {
      font-size: 18px;
    }
  }
}
.rating-button {
  width: 36px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  &:after {
    font-family: "Material Icons";
    font-size: 36px;
    line-height: 1;
  }
}
.rating-empty {
  color: var(--lighter-gray);
  &:after {
    content: "star_border";
  }
}
.rating-half {
  color: var(--darker-gray);
  &:after {
    content: "star_half";
  }
}
.rating-full {
  color: var(--darker-gray);
  &:after {
    content: "star";
  }
}
</style>
