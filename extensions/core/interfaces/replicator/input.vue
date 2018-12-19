<template>
  <div class="interface-repeater-container">
    <div v-for="(group, o) in localValue" :key="o" class="fields">
      <div v-for="(field, i) in group" :key="field.id" class="group">
        <span>{{ field.title }}</span>
        <div v-if="field.type === 'object'" class="box">
          <div v-for="(prop, key) in field.properties" :key="key">
            <span></span>
            <v-input
              type="text"
              v-if="prop.type === 'string'"
              @input="
                v => updateValue((localValue[o][field.title][i][key] = v))
              "
            ></v-input>
            <span v-else>Not supported</span>
          </div>
        </div>
        <!-- TEXT input -->
        <v-input
          type="text"
          v-else-if="field.type === 'string'"
          :value="value[field.title]"
          @input="v => updateValue({ [field.title]: v })"
        ></v-input>
      </div>
    </div>
    <div class="controls">
      <v-button
        v-for="(item, i) in parsedItems"
        :key="item.title"
        type="button"
        @click="addNewField(i)"
      >
        <i class="material-icons">add</i> new field {{ item.title }}
        {{ item.type }}
      </v-button>
    </div>
    <pre v-text="localValue"></pre>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    parsedItems() {
      return typeof this.options.items === "string"
        ? JSON.parse(this.options.items)
        : this.options.items;
    },
    localValue() {
      return JSON.parse(this.value);
    }
  },
  methods: {
    addNewField(i) {
      const field = this.parsedItems[i];
      const fieldTitle = field.title;
      const newValue = Object.assign({}, { [fieldTitle]: [] }, this.localValue);
      newValue[fieldTitle].push({ values: [], ...field });
      this.updateValue(newValue);
    },
    updateValue(newValue) {
      if (!newValue) return;
      const updatedValue = Object.assign({}, this.localValue, newValue);
      const value = this.value;
      console.log("actualización localvalue", {
        value,
        updatedValue,
        localValue: this.localValue,
        newValue
      });
      this.$nextTick(() => {
        this.$emit("input", JSON.stringify(updatedValue));
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.v-input {
  width: 100%;
  max-width: var(--width-medium);
}
pre {
  border: 1px solid gray;
  white-space: pre-wrap;
  font-family: monospace;
  background: var(--highlight);
}
.group {
  border: 1px solid gray;
}
</style>
