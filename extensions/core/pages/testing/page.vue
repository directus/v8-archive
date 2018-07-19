<template>
  <div class="example-page">
    <v-header-bar title="Page Extensions FTW!" />
    <h1>Hello World!</h1>
  </div>
</template>

<script>
export default {
  name: "first-page"
};
</script>

<style lang="scss" scoped>
.example-page {
  padding: var(--page-padding);
}
</style>
