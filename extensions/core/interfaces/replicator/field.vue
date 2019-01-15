<template>
  <div class="field">
    <label> {{ field.title }} </label>
    <div v-if="fieldIsObject" class="group">
      <ReplicatorField
        v-for="item in field.children"
        :key="item.title"
        :label="label"
        :field="item"
      ></ReplicatorField>
    </div>
    <select v-else-if="fieldIsSelect">
      <option v-for="opt in field.enum" :key="opt" :value="opt">
        {{ opt }}
      </option>
    </select>
    <input v-else-if="fieldIsSimple" :type="field.type" />
    <span v-else>Field type unknown {{ field.type }}</span>
  </div>
</template>

<script>
const simpleTypes = [
  "button",
  "checkbox",
  "color",
  "date ",
  "datetime-local",
  "email ",
  "file",
  "hidden",
  "image",
  "month ",
  "number ",
  "password",
  "radio",
  "range ",
  "reset",
  "search",
  "submit",
  "tel",
  "text",
  "time ",
  "url",
  "week"
];

export default {
  name: "ReplicatorField",
  props: {
    label: {
      type: String,
      default: ""
    },
    field: {
      type: Object,
      default: () => ({})
    }
  },
  computed: {
    fieldIsObject() {
      return this.field.type === "object";
    },
    fieldIsSelect() {
      return Array.isArray(this.field.enum);
    },
    fieldIsSimple() {
      return simpleTypes.includes(this.field.type);
    }
  }
};
</script>

<style scoped>
.field {
  margin-left: 1rem;
}
</style>
