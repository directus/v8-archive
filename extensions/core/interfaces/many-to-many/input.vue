<template>
  <div class="interface-many-to-many">
    <div v-if="relationSetup === false" class="notice">
      <p>
        <i class="material-icons">warning</i>
        {{ $t("interfaces-many-to-many-relation_not_setup") }}
      </p>
    </div>
    <template>
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
            :key="item[junctionPrimaryKey]"
            @click="editExisting = item"
          >
            <div v-for="column in columns" :key="column.field" class="no-wrap">
              <v-ext-display
                :interface-type="(column.fieldInfo || {}).interface || null"
                :name="column.field"
                :type="column.fieldInfo.type"
                :datatype="column.fieldInfo.datatype"
                :options="column.fieldInfo.options"
                :value="item[junctionRelatedKey][column.field]"
              />
            </div>
            <button
              type="button"
              class="remove-item"
              v-tooltip="$t('remove_related')"
              @click.stop="
                removeRelated({
                  junctionKey: item[junctionPrimaryKey],
                  relatedKey: item[junctionRelatedKey][relatedKey],
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
            :values="editExisting[junctionRelatedKey]"
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
  name: "interface-many-to-many",
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
      return this.relation.junction.collection_one.collection;
    },
    relatedCollectionFields() {
      return this.relation.junction.collection_one.fields;
    },
    junctionCollectionFields() {
      return this.relation.collection_many.fields;
    },
    relatedKey() {
      return this.$lodash.find(this.relation.junction.collection_one.fields, {
        primary_key: true
      }).field;
    },
    junctionPrimaryKey() {
      return this.$lodash.find(this.relation.collection_many.fields, {
        primary_key: true
      }).field;
    },
    junctionRelatedKey() {
      return this.relation.junction.field_many.field;
    },

    visibleFields() {
      if (this.relationSetup === false) return [];
      if (!this.options.fields) return [];
      return this.options.fields.split(",").map(val => val.trim());
    },
    items() {
      if (this.relationSetup === false) return null;

      return this.$lodash.orderBy(
        (this.value || [])
          .filter(val => !val.$delete)
          .filter(val => val[this.junctionRelatedKey] != null),
        item => item[this.junctionRelatedKey][this.sort.field],
        this.sort.asc ? "asc" : "desc"
      );
    },
    columns() {
      if (this.relationSetup === false) return null;

      return this.visibleFields.map(field => ({
        fieldInfo: this.relatedCollectionFields[field],
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

      this.selection = this.value
        .filter(val => !val.$delete)
        .filter(val => val[this.junctionRelatedKey] != null)
        .map(val => val[this.junctionRelatedKey]);
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
        // Filter out non-existing relationships (eg the related item has been
        // deleted)
        .filter(val => val[this.junctionRelatedKey])
        .map(val => val[this.junctionRelatedKey][this.relatedKey]);

      const selectedPKs = this.selection.map(item => item[this.relatedKey]);

      // Set $delete: true to all items that aren't selected anymore
      const newValue = (this.value || []).map(junctionRow => {
        const relatedPK = (junctionRow[this.junctionRelatedKey] || {})[
          this.relatedKey
        ];

        if (!relatedPK) return junctionRow;

        // If item was saved before, add $delete flag
        if (selectedPKs.includes(relatedPK) === false) {
          return {
            [this.junctionPrimaryKey]: junctionRow[this.junctionPrimaryKey],
            $delete: true
          };
        }

        // If $delete flag is set and the item is re-selected, remove $delete flag
        if (junctionRow.$delete && selectedPKs.includes(relatedPK)) {
          const clone = { ...junctionRow };
          delete clone.$delete;
          return clone;
        }

        return junctionRow;
      });

      // Fetch item values for all newly selected items
      const newSelection = selectedPKs.filter(
        pk => savedRelatedPKs.includes(pk) === false
      );

      (newSelection.length > 0
        ? this.$api.getItem(this.relatedCollection, newSelection.join(","))
        : Promise.resolve()
      )
        .then(res => {
          if (res) return res.data;
          return null;
        })
        .then(data => {
          if (data) {
            if (Array.isArray(data)) {
              data.forEach(row =>
                newValue.push({
                  [this.junctionRelatedKey]: row
                })
              );
            } else {
              newValue.push({
                [this.junctionRelatedKey]: data
              });
            }
          }

          this.$emit("input", newValue);

          this.selectExisting = false;
          this.selectionSaving = false;
        })
        .catch(error => {
          this.$events.emit("error", {
            notify: this.$t("something_went_wrong_body"),
            error
          });

          this.selectionSaving = false;
          this.selectExisting = false;
        });
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
        ...(this.value || [] || []).map(val => {
          if (val.id === this.editExisting[this.junctionPrimaryKey]) {
            return {
              ...val,
              [this.junctionRelatedKey]: {
                ...val[this.junctionRelatedKey],
                ...this.edits
              }
            };
          }

          return val;
        })
      ]);

      this.edits = {};
      this.editExisting = false;
    },
    addNewItem() {
      this.$emit("input", [
        ...(this.value || []),
        {
          [this.junctionRelatedKey]: this.edits
        }
      ]);

      this.edits = {};
      this.addNew = false;
    },
    removeRelated({ junctionKey, relatedKey, item }) {
      if (junctionKey) {
        this.$emit(
          "input",
          (this.value || []).map(val => {
            if (val[this.junctionPrimaryKey] === junctionKey) {
              return {
                [this.junctionPrimaryKey]: val[this.junctionPrimaryKey],
                $delete: true
              };
            }

            return val;
          })
        );
      } else if (!junctionKey && !relatedKey) {
        this.$emit(
          "input",
          (this.value || []).filter(val => {
            return this.$lodash.isEqual(val, item) === false;
          })
        );
      } else {
        this.$emit(
          "input",
          (this.value || []).filter(val => {
            return (
              (val[this.junctionRelatedKey] || {})[this.relatedKey] !==
              relatedKey
            );
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
  background-color: var(--accent);
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
    background-color: var(--accent-dark);
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

.items {
  height: calc(100% - var(--header-height) - 1px);
}
</style>
