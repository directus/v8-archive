<template>
  <div class="rating">
    <stars v-if="options.display == 'star'"
      :options="options"
      :rating.sync="rating"
      :readonly="readonly"
    ></stars>
    <div class="rating-value" v-if="options.display == 'number'">
      <v-input
        class="rating-input"
        type="number"
        min="0"
        icon-left="star"
        :maxlength="length"
        :disabled="readonly"
        v-model="rating"
      ></v-input>
      <span>/ {{ options.max_stars }}</span>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-rating",
  mixins: [mixin],
  components: {
    Stars: require("./stars.vue").default
  },
  computed: {
    rating: {
      get() {
        return Number(this.value) || 0;
      },
      set(value) {
        //? How to stop user from entering value, larger than number of stars
        if (Number(value) > this.options.max_stars) {
          value = this.options.max_stars;
        }
        this.$emit("input", value);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.rating-input {
  display: inline-block;
  margin-right: 5px;
}
.v-input {
  width: 100%;
  max-width: var(--width-small);
}
</style>
