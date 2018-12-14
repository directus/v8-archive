<template>
  <div class="interface-repeater-container">
    <div class="fields">
      <pre v-for="field in fields" :key="field.id">{{ field }}</pre>
    </div>
    <div class="controls">
      <v-button type="button" @click="addNewField()">
        <i class="material-icons">add</i> new field
      </v-button>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

function generateFakeGuid() {
  const s4 = () =>
    Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  return (
    s4() +
    s4() +
    "-" +
    s4() +
    "-" +
    s4() +
    "-" +
    s4() +
    "-" +
    s4() +
    s4() +
    s4()
  );
}

const fieldTemplate = Object.freeze({
  id: "",
  type: "text",
  value: "",
  meta: {},
  attributtes: ""
});

export default {
  name: "input-replicator",
  mixins: [mixin],
  data() {
    return {
      fields: []
    };
  },
  mounted() {
    this.init();
  },
  methods: {
    /**
     * @param {Number} level The deepnes of the field
     */
    addNewField() {
      this.fields.push(
        Object.assign({}, fieldTemplate, { id: generateFakeGuid() })
      );
    }
  }
};
</script>
