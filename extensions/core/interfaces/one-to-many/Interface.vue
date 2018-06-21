<template>
  <div class="interface-one-to-many">
    <template v-if="relationshipSetup">
      <ul>
        <li
          v-for="item in selectionInItems"
          :key="item[relationship.field]">
          {{ item[relationship.field] }}
        </li>
      </ul>
      <v-button @click="selecting = true">{{ $t('select_existing') }}</v-button>
      <portal to="modal" v-if="selecting">
        <v-modal
          :title="$t('interfaces-one-to-many-select_items')"
          @confirm="selecting = false"
          action-required>
          <v-listing
            view-type="tabular"
            :fields="fields"
            :items="items"
            :loading="loading"
            :lazy-loading="lazyLoading"
            :selection="selection"
            :view-options="viewOptions"
            :view-query="viewQuery"
            @options="setViewOptions"
            @select="selection = $event"
            @query="setViewQuery"
            @next-page="fetchNextPage" />
        </v-modal>
      </portal>
    </template>
    <p v-else><i class="material-icons">warning</i> Relationship hasn't been setup yet</p>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-one-to-many",
  mixins: [mixin],
  data() {
    return {
      selecting: false,

      loading: false,
      lazyLoading: false,
      items: [],
      error: null,

      viewQuery: {},
      viewOptions: {},
      selection: [],

      fields: {}
    };
  },
  computed: {
    relationshipSetup() {
      return this.relationship != null;
    },
    itemsByKey() {
      if (!this.relationship) return {};

      return this.$lodash.keyBy(this.items, this.relationship.field);
    },
    selectionInItems() {
      return this.selection.map(key => this.itemsByKey[key]);
    }
  },
  created() {
    if (this.relationship) {
      this.hydrate();
    }
  },
  watch: {
    relationship() {
      this.hydrate();
    },
    selection() {
      this.$emit("input", this.selectionInItems);
    }
  },
  methods: {
    hydrate() {
      if (!this.relationship) return;

      this.loading = true;
      Promise.all([
        this.$api.getFields(this.relationship.collection),
        this.$api.getItems(this.relationship.collection, { fields: "*.*" })
      ])
        .then(([fields, items]) => {
          this.loading = false;
          this.fields = this.$lodash.mapValues(
            this.$lodash.keyBy(fields.data, "field"),
            info => ({
              ...info,
              name: this.$helpers.formatTitle(info.field) // TODO: Map translation key to name field to support translatable field names #421 & #422
            })
          );
          this.items = items.data;
        })
        .catch(error => {
          this.loading = false;
          this.error = error;
        });
    },
    setViewQuery(updates) {
      this.viewQuery = Object.assign({}, this.viewQuery, updates);
    },
    setViewOptions(updates) {
      this.viewQuery = Object.assign({}, this.viewQuery, updates);
    },
    fetchNextPage() {}
  }
};
</script>
