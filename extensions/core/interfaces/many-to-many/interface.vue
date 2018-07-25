<template>
  <div class="interface-many-to-many">
    <template v-if="doneLoading">
      <div class="table" v-if="items.length">
        <div class="header">
          <div class="row">
            <button
              v-for="column in columns"
              type="button"
              :key="column.field"
              @click="changeSort(column.field)">
              {{ column.name }}
              <i v-if="sort.field === column.field" class="material-icons">
                {{ sort.asc ? 'arrow_downward' : 'arrow_upward' }}
              </i>
            </button>
          </div>
        </div>
        <div class="body">
          <div
            v-for="item in items"
            class="row"
            :key="item[junctionPrimaryKey.field]"
            @click="editExisting = item">
            <div
              v-for="column in columns"
              :key="column.field">{{ item[junctionRelatedKey][column.field] }}</div>
            <button
              type="button"
              class="remove-item"
              v-tooltip="$t('remove_related')"
              @click.stop="removeRelated({
                junctionKey: item[junctionPrimaryKey.field],
                relatedKey: item[junctionRelatedKey][relatedKey],
                item
              })">
              <i class="material-icons">close</i>
            </button>
          </div>
        </div>
      </div>
      <button type="button" class="style-btn select" @click="addNew = true">
        <i class="material-icons">add</i>
        {{ $t("add_new") }}
      </button>
      <button type="button" class="style-btn select" @click="selectExisting = true">
        <i class="material-icons">playlist_add</i>
        <span>{{ $t("select_existing") }}</span>
      </button>
    </template>
    <v-spinner v-else />

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
        @save="saveSelection">
        <v-item-listing
          :collection="relatedCollection"
          :filters="options.preferences && options.preferences.filters || []"
          :view-query="options.preferences && options.preferences.viewQuery || {}"
          :view-type="options.preferences && options.preferences.viewType || 'tabular'"
          :view-options="options.preferences && options.preferences.viewOptions || {}"
          :selection="selection"
          @options="() => {}"
          @select="selection = $event"
          @query="() => {}" />
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
        @save="saveEdits">
        <div class="edit-modal-body">
          <v-edit-form
            :fields="relatedCollectionFields"
            :values="editExisting[junctionRelatedKey]"
            @stage-value="stageValue" />
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
        @save="addNewItem">
        <div class="edit-modal-body">
          <v-edit-form
            :fields="relatedCollectionFields"
            :values="relatedDefaultsWithEdits"
            @stage-value="stageValue" />
        </div>
      </v-modal>
    </portal>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

// TODO
//
// (Seeing I don't have internet on this plane, consider this a GH issue)
//
// If you save and stay, the edit form only re-fetches the values of the fields
// 1 level deep. This causes the relational interfaces to stop working.
//
// When saving, the app should use the fields: *.*.* param so relationl interfaces
// will keep working. This means that the SDK has to be updated to accept parameters
// in PUT and POST requests and that the store action for saving items needs to be
// updated to utilize the param.
//
// TODO 2
//
// When hitting save, the edit form shows the savedValues for a brief second. This is due to the fact that the edit form uses a reactive combination of the defaultvalues, savedvalues and edits (kept in the store) to render the values in the edit form. As soon as the save is done, the edits are being cleared from the store before the promise is resolves. The moment the edit form triggers the next step in the promise chain, the edits have already been cleared, causing the reactive values to revert to the edit-less state for the period of time between the edits being cleared and whatever the edit page does (f.e. navigating away). TO fix this, we either have to find a way to clear the edits from the store _after_ the edit form is done with handling the cleanup / naviagtion of the page, or we should "lock" the values used on the edit form as soon as saving commences. I think the second way is easier to do, seeing I wouldn't have a clue to "go back up" the promise chain:
//
// Store:
//
// return Promise.resolve()
//   .then(clearEdits);
//
// Edit form:
//
// store.save
//   .then(navigate)
//
// TODO 3
//
// Allow the user to override the viewoptions / viewquery etc from the listing modal. Right now, the header stil lhas the hover for sort etc. These actions can be activated by merging in the overrides in the set preferences from options. We can store the overrides in a separate data key, and use a computed value for the merged key that's used by the item-listing component. BONUS: consider adding the search / filter widget _into_ the item listing modal? Food for thought.

export default {
  mixins: [mixin],
  name: "interface-many-to-many",
  data() {
    return {
      loading: false,
      error: null,

      relatedCollectionFields: null,
      junctionCollectionFields: null,

      sort: {
        field: null,
        asc: true
      },

      selectExisting: false,
      selectionSaving: false,
      selection: [],

      editExisting: null,
      addNew: null,
      edits: {}
    };
  },
  computed: {
    relatedSide() {
      const { collection_a, collection_b } = this.relationship;

      if (collection_a === this.currentCollection) return "b";

      return "a";
    },
    currentCollection() {
      return this.fields[this.name].collection;
    },
    relatedCollection() {
      return this.relationship["collection_" + this.relatedSide];
    },
    relatedKey() {
      return this.relationship["field_" + this.relatedSide];
    },
    junctionPrimaryKey() {
      if (!this.junctionCollectionFields) return null;

      return this.$lodash.find(this.junctionCollectionFields, {
        primary_key: true
      });
    },
    junctionRelatedKey() {
      return this.relationship["junction_key_" + this.relatedSide];
    },

    visibleFields() {
      if (!this.options.fields) return null;
      return this.options.fields.split(",").map(val => val.trim());
    },
    items() {
      return this.$lodash.orderBy(
        this.value.filter(val => !val.$delete),
        item => item[this.junctionRelatedKey][this.sort.field],
        this.sort.asc ? "asc" : "desc"
      );
    },
    columns() {
      return this.visibleFields.map(field => ({
        field,
        name: this.$helpers.formatTitle(field)
      }));
    },
    doneLoading() {
      return (
        this.relatedCollectionFields !== null &&
        this.junctionCollectionFields !== null
      );
    },
    relatedDefaultValues() {
      if (!this.relatedCollectionFields) return null;

      return this.$lodash.mapValues(
        this.relatedCollectionFields,
        field => field.default_value
      );
    },
    relatedDefaultsWithEdits() {
      if (!this.relatedDefaultValues) return null;

      return {
        ...this.relatedDefaultValues,
        ...this.edits
      };
    }
  },
  created() {
    this.sort.field = this.visibleFields[0];
    this.setSelection();
    this.getRelatedCollectionsFieldInfo();
  },
  watch: {
    value() {
      this.setSelection();
    }
  },
  methods: {
    setSelection() {
      this.selection = this.value
        .filter(val => !val.$delete)
        .map(val => val[this.junctionRelatedKey][this.relatedKey]);
    },
    getRelatedCollectionsFieldInfo() {
      const { junction_collection } = this.relationship;

      if (!junction_collection || !this.relatedCollection) return null;

      this.loading = true;

      Promise.all([
        this.$api.getFields(junction_collection),
        this.$api.getFields(this.relatedCollection)
      ])
        .then(([junctionRes, collectionRes]) => ({
          junctionFields: junctionRes.data,
          collectionFields: collectionRes.data
        }))
        .then(({ junctionFields, collectionFields }) => {
          this.relatedCollectionFields = this.$lodash.keyBy(
            collectionFields,
            "field"
          );
          this.junctionCollectionFields = this.$lodash.keyBy(
            junctionFields,
            "field"
          );
          this.loading = false;
        })
        .catch(error => {
          this.error = error;
          this.loading = false;
        });
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

      const savedRelatedPKs = this.value
        .filter(val => !val.$delete)
        .map(val => val[this.junctionRelatedKey][this.relatedKey]);

      // Set $delete: true to all items that aren't selected anymore
      const newValue = this.value.map(junctionRow => {
        const relatedPK = (junctionRow[this.junctionRelatedKey] || {})[this.relatedKey];

        if (!relatedPK) return junctionRow;

        // If item was saved before, add $delete flag
        if (this.selection.includes(relatedPK) === false) {
          return {
            [this.junctionPrimaryKey.field]: junctionRow[this.junctionPrimaryKey.field],
            $delete: true
          };
        }

        // If $delete flag is set and the item is re-selected, remove $delete flag
        if (junctionRow.$delete && this.selection.includes(relatedPK)) {
          const clone = { ...junctionRow };
          delete clone.$delete;
          return clone;
        }

        return junctionRow;
      });

      // Fetch item values for all newly selected items
      const newSelection = this.selection.filter(pk => savedRelatedPKs.includes(pk) === false);

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
              data.forEach(row => newValue.push({
                [this.junctionRelatedKey]: row
              }));
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
        ...this.value.map(val => {
          if (val.id === this.editExisting[this.junctionPrimaryKey.field]) {
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
        ...this.value,
        {
          [this.junctionRelatedKey]: this.edits
        }
      ]);

      this.edits = {};
      this.addNew = false;
    },
    removeRelated({ junctionKey, relatedKey, item }) {
      if (junctionKey) {
        this.$emit("input", this.value.map(val => {
          if (val[this.junctionPrimaryKey.field] === junctionKey) {
            return {
              [this.junctionPrimaryKey.field]: val[this.junctionPrimaryKey.field],
              $delete: true
            }
          }

          return val;
        }));
      } else if (!junctionKey && !relatedKey) {
        this.$emit("input", this.value.filter(val => {
          return this.$lodash.isEqual(val, item) === false;
        }));
      } else {
        this.$emit("input", this.value.filter(val => {
          return (val[this.junctionRelatedKey] || {} )[this.relatedKey] !== relatedKey
        }));
      }
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
</style>
