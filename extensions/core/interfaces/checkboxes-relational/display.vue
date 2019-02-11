<template>
  <v-popover trigger="hover" v-if="options.listing_template && this.value && this.value.length > 0">
    <div class="display-checkbox-relational">{{ itemCount }}</div>
    <template slot="popover">
      <ul class="list">
        <li v-for="(val, i) in value" :key="i">{{ render(val) }}</li>
      </ul>
    </template>
  </v-popover>

  <div v-else class="display-checkbox-relational">{{ itemCount }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "display-checkbox-relational",
  mixins: [mixin],
  computed: {
    itemCount() {
      return this.$tc("item_count", (this.value || []).length, {
        count: (this.value || []).length
      });
    }
  },
  methods: {
    render(val) {
      return this.$helpers.micromustache.render(
        this.options.listing_template,
        val
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.list {
  max-height: 200px;
  overflow-y: auto;
  list-style: none;
  padding: 0;
  margin: 0;
  li {
    color: var(--dark-gray);
    padding: 8px 0;
    &:not(:last-of-type) {
      border-bottom: 1px solid var(--lightest-gray);
    }
  }
}
</style>
