<template>
  <div>{{ displayValue }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  name: "display-user-role",
  computed: {
    displayValue() {
      return (
        this.value &&
        this.value[0] &&
        this.value[0].role &&
        this.value[0].role.name
      );
    }
  }
};
</script>

<style lang="scss" scoped></style>
