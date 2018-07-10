<template>
  <v-select
    :name="name"
    :id="name"
    :placeholder="options.placeholder"
    :options="selectOptions"
    :value="value"
    @input="$emit('input', $event)" />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-many-to-one",
  mixins: [mixin],
  data() {
    return {
      loading: false,
      error: null,
      items: []
    };
  },
  computed: {
    render() {
      return this.$helpers.micromustache.compile(this.options.template);
    },
    selectOptions() {
      if (this.items.length === 0) return {};

      return this.$lodash.mapValues(
        this.$lodash.keyBy(this.items, this.relationship.field),
        item => this.render(item)
      );
    }
  },
  created() {
    if (this.relationship) {
      this.fetchItems();
    }
  },
  watch: {
    relationship() {
      this.fetchItems();
    }
  },
  methods: {
    fetchItems() {
      if (this.relationship == null) return;

      const collection = this.relationship.collection;

      this.loading = true;

      const params = { fields: "*.*", limit: this.options.limit };

      this.$api
        .getItems(collection, params)
        .then(res => res.data)
        .then(items => {
          this.items = items;
          this.loading = false;
        })
        .catch(error => {
          this.error = error;
          this.loading = false;
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.v-select {
  margin-top: 0;
  max-width: var(--width-normal);
}
</style>
