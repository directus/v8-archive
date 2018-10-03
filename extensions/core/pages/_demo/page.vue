<template>
  <div class="demo-page">
    <v-header title="Demo Page"></v-header>
    <h1 class="style-0">Just an example..</h1>
    <p>This is a Directus Page Extension, you can put anything you want in here.</p>
  </div>
</template>

<script>
export default {
  name: "demo-page"
};
</script>

<style lang="scss" scoped>
.demo-page {
  padding: var(--page-padding);

  h1 {
    margin-bottom: 20px;
  }
}
</style>
