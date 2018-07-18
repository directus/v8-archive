<template>
    <hr 
    :class="`line-style-${options.style}`"
    />
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
hr {
  border-top: 1px solid var(--lighter-gray);
}
hr {
  border-top: 1px solid var(--lighter-gray);
}
</style>


