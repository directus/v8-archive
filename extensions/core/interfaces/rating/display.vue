<template>
  <div class="display-rating">
    <!-- If 'number' is selected for display option -->
    <div class="rating-numbers" v-if="options.display=='number'">
      <i class="material-icons">star</i>
      <span>{{value}}</span>
    </div>

    <!-- If 'star' is selected for display option -->
    <div class="rating-numbers" v-if="options.display=='star'">
      <stars :options="options" :rating="value" :readonly="true"></stars>
    </div>

  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  components: {
    Stars: require("./stars.vue").default
  }
};
</script> 

<style lang="scss" scoped>
.rating-numbers {
  display: flex;
  align-items: center;
  i.material-icons {
    font-size: 18px;
    line-height: 1;
    margin-right: 4px;
  }
}
</style>
