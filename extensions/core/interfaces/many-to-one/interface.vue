<template>
  <v-select
    :name="name"
    :id="name"
    :placeholder="options.placeholder"
    :options="selectOptions"
    :value="valuePK"
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
    valuePK() {
      if (this.$lodash.isObject(this.value)) return String(this.value[this.relatedKey]);
      return String(this.value);
    },
    render() {
      return this.$helpers.micromustache.compile(this.options.template);
    },
    selectOptions() {
      if (this.items.length === 0) return {};

      return this.$lodash.mapValues(
        this.$lodash.keyBy(this.items, this.relatedKey),
        item => this.render(item)
      );
    },
    currentCollection() {
      if (this.relationshipSetup === false) return null;
      return this.fields[this.name].collection;
    },
    relatedSide() {
      if (this.relationshipSetup === false) return null;
      const { collection_a, collection_b } = this.relationship;

      if (collection_a === this.currentCollection) return "b";

      return "a";
    },
    relatedCollection() {
      if (this.relationshipSetup === false) return null;
      return this.relationship["collection_" + this.relatedSide];
    },
    relatedKey() {
      if (this.relationshipSetup === false) return null;
      return this.relationship["field_" + this.relatedSide];
    },
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

      const collection = this.relatedCollection;

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
