<template>
    <div>
        <i class="material-icons">{{ value }}</i>
    </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import icons from "./icons.json";

export default {
  mixins: [mixin],
  computed: {
    icons() {
      return icons;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>

