<template>
  <div class="rating">
    <stars :options="options" :rating.sync="rating" :readonly="readonly"></stars>
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
  components: {
    Stars: require("./stars.vue").default
  },
  computed: {
    rating: {
      get() {
        return this.value;
      },
      set(value) {
        //? How to stop user from entering value, larger than number of stars
        if (value > this.options.max_stars) {
          value = this.options.max_stars;
        }
        this.$emit("input", value);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.rating {
  display: flex;
  align-items: center;
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
