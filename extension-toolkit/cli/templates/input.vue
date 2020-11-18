<template>
  <input @input="emitValue" :value="value" />
</template>

<script>
  import mixin from "@directus/extension-toolkit/mixins/interface";

  export default {
    mixins: [mixin],
    methods: {
      emitValue(event) {
        const value = event.target.value;
        this.$emit("input", value);
      }
    }
  }
</script>

<style lang="scss" scoped>
input {
  border-radius: var(--border-radius);
}
</style>
