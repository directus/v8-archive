<template>
    <rating
      :rating="rating"
      :count="maxRating" 
      :readonly="readonly" 
      :disabled="disabled"
      :color="color"
    />
</template>

<script>
import rating from "./star-rating.vue";
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    rating() {
      return ((this.options.value > 0) ? this.options.value : 0);
    },
    maxRating() {
      return ((this.options.max >= 1) ? this.options.max : 5);
    },
    color() {
      return ((this.options.color) ? "yellow" : "blue");
    },
    disabled() {
      return this.options.disabled;
    }
  },
  components: {
    rating
  }
};
</script>
