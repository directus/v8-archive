<template>
  <div class="interface-status">
    <v-radio
      v-for="(options, key) in optionValues"
      :id="`${fields[name].collection}-${name}-${key}`"
      :name="name"
      :value="key"
      :key="key"
      :disabled="readonly"
      :model-value="String(value)"
      :label="$t(options.name)"
      :checked="key == value"
      @change="$emit('input', $event)"
    ></v-radio>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-status",
  mixins: [mixin],
  data() {
    return {
      startStatus: null
    };
  },
  computed: {
    statusMapping() {
      if (typeof this.options.status_mapping === "string") {
        return this.options.status_mapping
          ? JSON.parse(this.status_mapping)
          : {};
      }

      return this.options.status_mapping || {};
    },
    optionValues() {
      const allStatuses = Object.keys(this.statusMapping);

      const allowedStatuses = this.$lodash.differenceWith(
        allStatuses,
        this.blacklist,
        this.$lodash.isEqual
      );

      return this.$lodash.pick(this.statusMapping, allowedStatuses);
    },
    blacklist() {
      if (typeof this.permissions.status_blacklist === "string")
        return this.permissions.status_blacklist.split(",");

      return this.permissions.status_blacklist;
    },
    permissions() {
      if (this.newItem) {
        return this.$store.state.permissions[this.collection].$create;
      }

      return this.$store.state.permissions[this.collection].statuses[
        this.startStatus
      ];
    },
    collection() {
      return Object.values(this.fields)[0].collection;
    }
  },
  created() {
    this.startStatus = this.value;
  }
};
</script>

<style lang="scss" scoped>
.interface-status {
  max-width: var(--width-x-large);
  .v-radio {
    display: inline-block;
    margin-right: 40px;
    margin-bottom: 20px;
  }
}
</style>
