<template>
  <div class="readonly-many-to-many">
    {{ $tc('item_count', (value || []).length, { count: (value || []).length })}}
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-many-to-many",
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
</style>
