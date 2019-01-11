<template>
  <v-popover trigger="hover" v-if="options.template && options.template.length > 0">
    <div class="readonly-many-to-many">
      {{ itemCount }}
    </div>

    <template slot="popover">
      <ul class="list">
        <li v-for="(val, i) in value" :key="i">{{ render(val) }}</li>
      </ul>
    </template>
  </v-popover>

  <div v-else class="readonly-many-to-many">
    {{ itemCount }}
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-many-to-many",
  mixins: [mixin],
  computed: {
    itemCount() {
      return this.$tc("item_count", (this.value || []).length, { count: (this.value || []).length });
    }
  },
  methods: {
    render(val) {
      return this.$helpers.micromustache.render(this.options.template, val);
    }
  }
};
</script>

<style lang="scss" scoped>
.list {
  max-height: 200px;
  overflow-y: scroll;

  list-style: none;
  padding: 0;

  li {
    color: var(--dark-gray);
    padding: 8px 0;

    &:not(:last-of-type) {
      border-bottom: 1px solid var(--lightest-gray);
    }
  }
}
</style>
