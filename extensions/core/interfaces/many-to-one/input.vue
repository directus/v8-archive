<template>
  <div class="interface-many-to-one">

    <div v-if="relationshipSetup === false" class="notice">
      <p><i class="material-icons">warning</i> {{ $t('interfaces-many-to-many-relationship_not_setup') }}</p>
    </div>

    <template v-else>
      <v-select
        :name="name"
        :id="name"
        :placeholder="options.placeholder"
        :options="selectOptions"
        :value="valuePK"
        :icon="options.icon"
        @input="$emit('input', $event)" />

      <button v-if="count > 10" type="button" @click="showListing = true" />

      <v-spinner
        v-show="loading"
        line-fg-color="var(--light-gray)"
        line-bg-color="var(--lighter-gray)"
        class="spinner" />

      <portal to="modal" v-if="showListing">
        <v-modal
          :title="$t('select_existing')"
          :buttons="{
            save: {
              text: 'save',
              color: 'accent',
              loading: selectionSaving,
              disabled: newSelected === null
            }
          }"
          @close="dismissModal"
          @save="populateDropdown">
          <v-items
            :collection="relatedCollection"
            :selection="[newSelected || valuePK]"
            :filters="filters"
            :view-query="viewQuery"
            :view-type="viewType"
            :view-options="viewOptions"
            @options="setViewOptions"
            @query="setViewQuery"
            @select="newSelected = $event[$event.length - 1]" />
        </v-modal>
      </portal>
    </template>
  </div>
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
      items: [],
      count: null,

      showListing: false,
      selectionSaving: false,
      newSelected: null,

      viewOptionsOverride: {},
      viewTypeOverride: null,
      viewQueryOverride: {},
      filtersOverride: []
    };
  },
  computed: {
    relationshipSetup() {
      if (!this.relationship) return false;

      const {
        field_a,
        field_b,
        collection_a,
        collection_b
      } = this.relationship;

      return (field_a && field_b && collection_a && collection_b) || false;
    },
    valuePK() {
      if (this.$lodash.isObject(this.value)) return this.value[this.relatedKey];
      return this.value;
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
    preferences() {
      return typeof this.options.preferences === "string"
        ? JSON.parse(this.options.preferences)
        : this.options.preferences;
    },
    filters() {
      if (this.relationshipSetup === false) return null;
      return [
        ...((this.preferences && this.preferences.filters) || []),
        ...this.filtersOverride
      ];
    },
    viewOptions() {
      if (this.relationshipSetup === false) return null;

      const viewOptions =
        (this.preferences && this.preferences.viewOptions) || {};
      return {
        ...viewOptions,
        ...this.viewOptionsOverride
      };
    },
    viewType() {
      if (this.relationshipSetup === false) return null;
      if (this.viewTypeOverride) return this.viewTypeOverride;
      return (this.preferences && this.preferences.viewType) || "tabular";
    },
    viewQuery() {
      if (this.relationshipSetup === false) return null;
      const viewQuery = (this.preferences && this.preferences.viewQuery) || {};
      return {
        ...viewQuery,
        ...this.viewQueryOverride
      };
    }
  },
  created() {
    if (this.relationshipSetup) {
      this.fetchItems();
    }
  },
  watch: {
    relationship() {
      if (this.relationshipSetup) {
        this.fetchItems();
      }
    }
  },
  methods: {
    fetchItems() {
      if (this.relationship == null) return;

      const collection = this.relatedCollection;

      this.loading = true;

      const params = { fields: "*.*", meta: "total_count", limit: 10 };

      return Promise.all([
        this.$api.getItems(collection, params),
        this.value ? this.$api.getItem(collection, this.valuePK) : null
      ])
        .then(([{ meta, data: items }, singleRes]) => {
          if (singleRes) {
            this.items = [...items, singleRes.data];
          } else {
            this.items = items;
          }

          this.loading = false;
          this.count = meta.total_count;
        })
        .catch(error => {
          console.error(error);
          this.error = error;
          this.loading = false;
        });
    },
    populateDropdown() {
      let exists = false;
      this.selectionSaving = true;

      this.items.forEach(item => {
        if (item[this.relatedKey] === this.newSelected) {
          exists = true;
        }
      });

      if (exists === false) {
        this.$api
          .getItem(this.relatedCollection, this.newSelected)
          .then(res => res.data)
          .then(item => {
            this.$emit("input", this.newSelected);
            this.items = [...this.items, item];
            this.selectionSaving = false;
            this.showListing = false;
          })
          .catch(error => {
            console.error(error);
            this.$events.emit("error", {
              notify: this.$t("something_went_wrong_body"),
              error
            });
          });
      } else {
        this.$emit("input", this.newSelected);
        this.selectionSaving = false;
        this.showListing = false;
      }
    },
    dismissModal() {
      this.showListing = false;
      this.selectionSaving = false;
      this.newSelected = null;
    },
    setViewOptions(updates) {
      this.viewOptionsOverride = {
        ...this.viewOptionsOverride,
        ...updates
      };
    },
    setViewQuery(updates) {
      this.viewQueryOverride = {
        ...this.viewQueryOverride,
        ...updates
      };
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-many-to-one {
  position: relative;
  max-width: var(--width-normal);
}

.v-select {
  margin-top: 0;
}

button {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: transparent;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  transition: border var(--fast) var(--transition);

  &:hover {
    transition: none;
    border-color: var(--light-gray);
  }
}

.spinner {
  position: absolute;
  right: -50px;
  top: 50%;
  transform: translateY(-50%);
}
</style>
