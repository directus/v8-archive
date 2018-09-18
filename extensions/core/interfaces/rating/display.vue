<template>
  <div class="display-rating">
    <i class="material-icons">star</i>
    <span>{{value}}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script> 

<style lang="scss" scoped>
.display-rating {
  display: flex;
  align-items: center;
  i.material-icons {
    font-size: 18px;
    line-height: 1;
    margin-right: 4px;
  }
}
</style>
