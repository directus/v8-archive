<template>
    <hr/>
</template>

<script>
import mixin from "../../../mixins/interface";
export default {
  mixins: [mixin],
  computed: {
    hello() {
      console.log("yo");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>


