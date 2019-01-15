<template>
  <div class="controls">
    <v-button
      v-for="(item, i) in config"
      :key="item.title"
      type="button"
      @click="$emit('add-field', i)"
    >
      <i class="material-icons">add</i> {{ item.title }}
    </v-button>
  </div>
</template>

<script>
export default {
  name: "ReplicatorControls",
  props: {
    config: {
      type: Array,
      default: () => []
    }
  }
};
</script>

<style scoped>
.controls {
  display: flex;
}
.style-btn {
  margin-right: 0.5rem;
}
</style>
