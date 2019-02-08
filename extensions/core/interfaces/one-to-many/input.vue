<template>
  <div class="interface-one-to-many">
    <div v-if="relationSetup === false" class="notice">
      <p>
        <i class="material-icons">warning</i>
        {{ $t("interfaces-one-to-many-relation_not_setup") }}
      </p>
    </div>
    <template v-else>
      <div class="table" v-if="items.length">
        <div class="header">
          <div class="row">
            <button
              v-for="column in columns"
              type="button"
              :key="column.field"
              @click="changeSort(column.field)"
            >
              {{ column.name }}
              <i v-if="sort.field === column.field" class="material-icons">
                {{ sort.asc ? "arrow_downward" : "arrow_upward" }}
              </i>
            </button>
          </div>
        </div>
        <div class="body">
          <div
            v-for="item in items"
            class="row"
            :key="item[relatedKey]"
            @click="editExisting = item"
          >
            <div v-for="column in columns" :key="column.field">
              {{ item[column.field] }}
            </div>
            <button
              type="button"
              class="remove-item"
              v-tooltip="$t('remove_related')"
              @click.stop="
                removeRelated({
                  relatedKey: item[relatedKey],
                  item
                })
              "
            >
              <i class="material-icons">close</i>
            </button>
          </div>
        </div>
      </div>
      <button type="button" class="style-btn select" @click="addNew = true">
        <i class="material-icons">add</i> {{ $t("add_new") }}
      </button>
      <button
        type="button"
        class="style-btn select"
        @click="selectExisting = true"
      >
        <i class="material-icons">playlist_add</i>
        <span>{{ $t("select_existing") }}</span>
      </button>
    </template>

    <portal to="modal" v-if="selectExisting">
      <v-modal
        :title="$t('select_existing')"
        :buttons="{
          save: {
            text: 'save',
            color: 'accent',
            loading: selectionSaving
          }
        }"
        @close="dismissSelection"
        @save="saveSelection"
      >
        <div class="search">
          <v-input
            type="search"
            :placeholder="$t('search')"
            class="search-input"
            @input="onSearchInput" />
        </div>
        <v-items
          :collection="relatedCollection"
          :filters="filters"
          :view-query="viewQuery"
          :view-type="viewType"
          :view-options="viewOptions"
          :selection="selection"
          @options="setViewOptions"
          @query="setViewQuery"
          @select="selection = $event"
        ></v-items>
      </v-modal>
    </portal>

    <portal to="modal" v-if="editExisting">
      <v-modal
        :title="$t('editing_item')"
        :buttons="{
          save: {
            text: 'save',
            color: 'accent',
            loading: selectionSaving
          }
        }"
        @close="editExisting = false"
        @save="saveEdits"
      >
        <div class="edit-modal-body">
          <v-form
            :fields="relatedCollectionFields"
            :values="editExisting"
            @stage-value="stageValue"
          ></v-form>
        </div>
      </v-modal>
    </portal>

    <portal to="modal" v-if="addNew">
      <v-modal
        :title="$t('creating_item')"
        :buttons="{
          save: {
            text: 'save',
            color: 'accent',
            loading: selectionSaving
          }
        }"
        @close="addNew = null"
        @save="addNewItem"
      >
        <div class="edit-modal-body">
          <v-form
            :fields="relatedCollectionFields"
            :values="relatedDefaultsWithEdits"
            @stage-value="stageValue"
          ></v-form>
        </div>
      </v-modal>
    </portal>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  name: "interface-one-to-many",
  data() {
    return {
      sort: {
        field: null,
        asc: true
      },

      selectExisting: false,
      selectionSaving: false,
      selection: [],

      editExisting: null,
      addNew: null,
      edits: {},

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
    currentCollection() {
      return this.relation.collection_one.collection;
    },
    relatedCollection() {
      return this.relation.collection_many.collection;
    },
    relatedCollectionFields() {
      return this.relation.collection_many.fields;
    },
    relatedKey() {
      return this.$lodash.find(this.relation.collection_many.fields, {
        primary_key: true
      }).field;
    },

    visibleFields() {
      if (this.relationSetup === false) return [];
      if (!this.options.fields) return [];

      if (Array.isArray(this.options.fields)) {
        return this.options.fields.map(val => val.trim());
      }

      return this.options.fields.split(",").map(val => val.trim());
    },
    items() {
      if (this.relationSetup === false) return [];

      return this.$lodash.orderBy(
        (this.value || []).filter(val => !val.$delete),
        item => item[this.sort.field],
        this.sort.asc ? "asc" : "desc"
      );
    },
    columns() {
      if (this.relationSetup === false) return null;
      return this.visibleFields.map(field => ({
        field,
        name: this.$helpers.formatTitle(field)
      }));
    },
    relatedDefaultValues() {
      if (this.relationSetup === false) return null;
      if (!this.relatedCollectionFields) return null;

      return this.$lodash.mapValues(
        this.relatedCollectionFields,
        field => field.default_value
      );
    },
    relatedDefaultsWithEdits() {
      if (this.relationSetup === false) return null;
      if (!this.relatedDefaultValues) return null;

      return {
        ...this.relatedDefaultValues,
        ...this.edits
      };
    },

    filters() {
      if (this.relationSetup === false) return null;
      return [
        ...((this.options.preferences && this.options.preferences.filters) ||
          []),
        ...this.filtersOverride
      ];
    },
    viewOptions() {
      if (this.relationSetup === false) return null;
      const viewOptions =
        (this.options.preferences && this.options.preferences.viewOptions) ||
        {};
      return {
        ...viewOptions,
        ...this.viewOptionsOverride
      };
    },
    viewType() {
      if (this.relationSetup === false) return null;
      if (this.viewTypeOverride) return this.viewTypeOverride;
      return (
        (this.options.preferences && this.options.preferences.viewType) ||
        "tabular"
      );
    },
    viewQuery() {
      if (this.relationSetup === false) return null;
      const viewQuery =
        (this.options.preferences && this.options.preferences.viewQuery) || {};
      return {
        ...viewQuery,
        ...this.viewQueryOverride
      };
    }
  },
  created() {
    if (this.relationSetup) {
      this.sort.field = this.visibleFields && this.visibleFields[0];
      this.setSelection();
    }

    this.onSearchInput = this.$lodash.debounce(this.onSearchInput, 200);
  },
  watch: {
    value() {
      this.setSelection();
    },
    relation() {
      if (this.relationSetup) {
        this.sort.field = this.visibleFields && this.visibleFields[0];
        this.setSelection();
      }
    }
  },
  methods: {
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
    setSelection() {
      if (!this.value) return;
      this.selection = this.value.filter(val => !val.$delete);
    },
    changeSort(field) {
      if (this.sort.field === field) {
        this.sort.asc = !this.sort.asc;
        return;
      }

      this.sort.asc = true;
      this.sort.field = field;
      return;
    },
    saveSelection() {
      this.selectionSaving = true;

      const savedRelatedPKs = (this.value || [])
        .filter(val => !val.$delete)
        .map(val => val[this.relatedKey]);

      const selectedPKs = this.selection.map(item => item[this.relatedKey]);

      // Set $delete: true to all items that aren't selected anymore
      const newValue = (this.value || []).map(item => {
        const relatedPK = item[this.relatedKey];

        if (!relatedPK) return item;

        // If item was saved before, add $delete flag
        if (selectedPKs.includes(relatedPK) === false) {
          return {
            [this.relatedKey]: item[this.relatedKey],
            $delete: true
          };
        }

        // If $delete flag is set and the item is re-selected, remove $delete flag
        if (item.$delete && selectedPKs.includes(relatedPK)) {
          const clone = { ...item };
          delete clone.$delete;
          return clone;
        }

        return item;
      });

      selectedPKs.forEach((selectedPK, i) => {
        if (savedRelatedPKs.includes(selectedPK) === false) {
          const item = { ...this.selection[i] };
          delete item[this.relation.field_many.field];
          newValue.push(item);
        }
      });

      this.$emit("input", newValue);

      this.selectExisting = false;
      this.selectionSaving = false;
    },
    dismissSelection() {
      this.setSelection();
      this.selectExisting = false;
    },
    stageValue({ field, value }) {
      this.$set(this.edits, field, value);
    },
    saveEdits() {
      this.$emit("input", [
        ...(this.value || []).map(val => {
          if (val.id === this.editExisting[this.relatedKey]) {
            return {
              ...val,
              ...this.edits
            };
          }

          return val;
        })
      ]);

      this.edits = {};
      this.editExisting = false;
    },
    addNewItem() {
      this.$emit("input", [...(this.value || []), this.edits]);

      this.edits = {};
      this.addNew = false;
    },
    removeRelated({ relatedKey, item }) {
      if (relatedKey) {
        this.$emit(
          "input",
          (this.value || []).map(val => {
            if (val[this.relatedKey] === relatedKey) {
              return {
                [this.relatedKey]: val[this.relatedKey],
                $delete: true
              };
            }

            return val;
          })
        );
      } else {
        this.$emit(
          "input",
          (this.value || []).filter(val => {
            return val[this.relatedKey] !== relatedKey;
          })
        );
      }
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
.table {
  background-color: var(--white);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  border-spacing: 0;
  width: 100%;
  margin: 10px 0 20px;

  .header {
    height: var(--input-height);
    border-bottom: 1px solid var(--lighter-gray);

    button {
      text-align: left;
      color: var(--gray);
      font-size: 10px;
      text-transform: uppercase;
      font-weight: 700;
      transition: color var(--fast) var(--transition);

      &:hover {
        transition: none;
        color: var(--darker-gray);
      }
    }

    i {
      font-size: 12px;
      vertical-align: top;
      color: var(--light-gray);
    }
  }

  .row {
    display: flex;
    align-items: center;
    padding: 0 5px;

    > div {
      padding: 3px 5px;
      flex-basis: 200px;
    }
  }

  .header .row {
    align-items: center;
    height: 40px;

    & > button {
      padding: 3px 5px;
      flex-basis: 200px;
    }
  }

  .body {
    max-height: 275px;
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;

    .row {
      cursor: pointer;
      position: relative;
      height: 50px;
      border-bottom: 1px solid var(--lightest-gray);

      &:hover {
        background-color: var(--highlight);
      }

      & div:last-of-type {
        flex-grow: 1;
      }

      button {
        color: var(--lighter-gray);
        transition: color var(--fast) var(--transition);

        &:hover {
          transition: none;
          color: var(--danger);
        }
      }
    }
  }
}

button.select {
  background-color: var(--darker-gray);
  border-radius: var(--border-radius);
  height: var(--input-height);
  padding: 0 10px;
  display: inline-flex;
  align-items: center;
  margin-right: 10px;
  transition: background-color var(--fast) var(--transition);

  i {
    margin-right: 5px;
  }

  &:hover {
    transition: none;
    background-color: var(--darkest-gray);
  }
}

.edit-modal-body {
  padding: 20px;
  background-color: var(--body-background);
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
</style>
