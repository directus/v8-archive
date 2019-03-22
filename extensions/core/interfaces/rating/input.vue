<template>
  <div class="rating">
    <stars
      v-if="options.display == 'star'"
      :options="options"
      :rating.sync="rating"
      :readonly="readonly"
    ></stars>
    <div class="rating-value" v-if="options.display == 'number'">
      <v-input
        class="rating-input"
        type="number"
        min="0"
        :max="options.max_stars"
        icon-left="star"
        :disabled="readonly"
        :value="String(value) || '0'"
        @input="updateValue"
      ></v-input>
      <span>/ {{ options.max_stars }}</span>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import Stars from "./stars.vue";

export default {
  name: "interface-rating",
  mixins: [mixin],
  components: {
    Stars
  },
  methods: {
    updateValue(value) {
      if (value > this.options.max_stars) {
        event.target.value = String(this.options.max_stars);
        return this.$emit("input", this.options.max_stars);
      }

      this.$emit("input", +value);
    }
  }
};
</script>

<style lang="scss" scoped>
.rating-input {
  display: inline-block;
  margin-right: 5px;
  width: 100%;
  max-width: var(--width-small);
}
</style>
