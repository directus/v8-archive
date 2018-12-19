<template>
  <div>
    <v-input
      type="text"
      v-if="property.type === 'string'"
      @input="v => $emit('input', v)"
    ></v-input>
    <span v-else>Not supported</span>
  </div>
</template>

<script>
export default {
  name: "RecursiveField",
  props: {
    property: {
      type: Object,
      required: true
    },
    key: {
      type: String,
      default: "Key"
    }
  }
};
</script>

<style scoped></style>
