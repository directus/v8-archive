<template>
  <div>
    {{ items }}
  </div>
</template>

<script>
  import mixin from "@directus/extension-toolkit/mixins/layout";

  export default {
    mixins: [mixin]
  }
</script>

<style lang="scss" scoped>
div {
  background-color: var(--red-300);
}
</style>
