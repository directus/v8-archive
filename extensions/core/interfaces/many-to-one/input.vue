<template>
  <div class="interface-many-to-one">
    <div v-if="relationSetup === false" class="notice">
      <p>
        <i class="material-icons">warning</i>
        {{ $t("interfaces-many-to-many-relation_not_setup") }}
      </p>
    </div>

    <template v-else>
      <v-select
        :name="name"
        :id="name"
        :placeholder="options.placeholder || ''"
        :options="selectOptions"
        :value="valuePK"
        :icon="options.icon"
        @input="$emit('input', $event)"
      ></v-select>

      <button
        v-if="count > options.threshold"
        type="button"
        @click="showListing = true"
      ></button>

      <v-spinner
        v-show="loading"
        line-fg-color="var(--light-gray)"
        line-bg-color="var(--lighter-gray)"
        class="spinner"
      ></v-spinner>

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
          @save="populateDropdown"
          action-required
        >
          <div class="search">
            <v-input
              type="search"
              :placeholder="$t('search')"
              class="search-input"
              @input="onSearchInput" />
          </div>
          <v-items
            class="items"
            :collection="relation.collection_one.collection"
            :selection="selection"
            :filters="filters"
            :view-query="viewQuery"
            :view-type="viewType"
            :view-options="viewOptions"
            @options="setViewOptions"
            @query="setViewQuery"
            @select="emitValue"
          ></v-items>
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
    relationSetup() {
      if (!this.relation) return false;
      return true;
    },
    relatedPrimaryKeyField() {
      return this.$lodash.find(this.relation.collection_one.fields, {
        primary_key: true
      }).field;
    },
    valuePK() {
      if (this.$lodash.isObject(this.value))
        return this.value[this.relatedPrimaryKeyField];

      return this.value;
    },
    render() {
      return this.$helpers.micromustache.compile(this.options.template);
    },
    selection() {
      if (!this.value) return [];

      if (this.newSelected) {
        return [this.newSelected];
      }

      if (this.valuePK) {
        return [{ [this.relatedPrimaryKeyField]: this.valuePK }];
      }

      return [];
    },
    selectOptions() {
      if (this.items.length === 0) return {};

      return this.$lodash.mapValues(
        this.$lodash.keyBy(this.items, this.relatedPrimaryKeyField),
        item => this.render(item)
      );
    },
    preferences() {
      return typeof this.options.preferences === "string"
        ? JSON.parse(this.options.preferences)
        : this.options.preferences;
    },
    filters() {
      if (this.relationSetup === false) return null;
      return [
        ...((this.preferences && this.preferences.filters) || []),
        ...this.filtersOverride
      ];
    },
    viewOptions() {
      if (this.relationSetup === false) return null;

      const viewOptions =
        (this.preferences && this.preferences.viewOptions) || {};
      return {
        ...viewOptions,
        ...this.viewOptionsOverride
      };
    },
    viewType() {
      if (this.relationSetup === false) return null;
      if (this.viewTypeOverride) return this.viewTypeOverride;
      return (this.preferences && this.preferences.viewType) || "tabular";
    },
    viewQuery() {
      if (this.relationSetup === false) return null;
      const viewQuery = (this.preferences && this.preferences.viewQuery) || {};
      return {
        ...viewQuery,
        ...this.viewQueryOverride
      };
    }
  },
  created() {
    if (this.relationSetup) {
      this.fetchItems();
    }

    this.onSearchInput = this.$lodash.debounce(this.onSearchInput, 200);
  },
  watch: {
    relation() {
      if (this.relationSetup) {
        this.fetchItems();
      }
    }
  },
  methods: {
    emitValue(selection) {
      if (selection.length === 1) {
        this.newSelected = selection[0];
      } else if (selection.length === 0) {
        this.newSelected = null;
      } else {
        this.newSelected = selection[selection.length - 1];
      }

      this.$emit("input", this.newSelected);
    },
    fetchItems() {
      if (this.relation == null) return;

      const collection = this.relation.collection_one.collection;

      this.loading = true;

      const params = { fields: "*.*", meta: "total_count", limit: this.options.threshold };

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
        if (
          item[this.relatedPrimaryKeyField] ===
          this.newSelected[this.relatedPrimaryKeyField]
        ) {
          exists = true;
        }
      });

      if (exists === false) {
        this.$api
          .getItem(
            this.relation.collection_one.collection,
            this.newSelected[this.relatedPrimaryKeyField]
          )
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
    },
    onSearchInput(value) {
      this.setViewQuery({
        q: value
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-many-to-one {
  position: relative;
  max-width: var(--width-medium);
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

.search-input {
  border-bottom: 1px solid var(--lightest-gray);

  &/deep/ input {
    border-radius: 0;
    border: none;
    padding-left: var(--page-padding);
    height: var(--header-height);

    &::placeholder {
      color: var(--light-gray);
    }
  }
}

.items {
  height: calc(100% - var(--header-height) - 1px);
}
</style>
