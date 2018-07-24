<template>
  <div class="interface-many-to-many">
    <template v-if="doneLoading">
      <div class="table">
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
            :key="item[relatedPrimaryKey]"
            @click="editExisting = item">
            <div
              v-for="column in columns"
              :key="column.field">{{ item[column.field] }}</div>
            <button
              type="button"
              class="remove-item"
              @click.stop="warnRemoveitem(item.id)">
              <i class="material-icons">close</i>
            </button>
          </div>
        </div>
      </div>
      <button type="button" class="style-btn select">
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
        action-required
        :buttons="{
          save: {
            text: 'save',
            color: 'accent',
            loading: selectionSaving
          }
        }"
        @save="saveSelection">
        <v-item-listing
          :collection="relationship.collection"
          :filters="filters"
          :search-query="searchQuery"
          :view-query="viewQuery"
          :view-type="viewType"
          :view-options="viewOptions"
          :selection="selection"
          @options="() => {}"
          @select="selection = $event"
          @query="() => {}" />
      </v-modal>
    </portal>

    <portal to="modal" v-if="editExisting !== null">
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
        @save="saveItem">
        <div class="edit-modal-body">
          <v-edit-form :fields="relatedCollectionFields" :values="editExisting" />
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
      filters: [],
      searchQuery: null,
      viewType: "tabular",
      viewQuery: {},
      viewOptions: {},

      editExisting: null
    };
  },
  computed: {
    visibleFields() {
      if (!this.options.fields) return null;
      return this.options.fields.split(",").map(val => val.trim());
    },
    items() {
      const items = this.value.map(val => val.movie);

      return this.$lodash.orderBy(
        items,
        this.sort.field,
        this.sort.asc ? "asc" : "desc"
      );
    },
    columns() {
      return this.visibleFields.map(field => ({
        field,
        name: this.$helpers.formatTitle(field)
      }));
    },
    relatedPrimaryKey() {
      if (!this.relatedCollectionFields) return null;

      return this.$lodash.find(this.relatedCollectionFields, { primary_key: true });
    },
    junctionPrimaryKey() {
      if (!this.junctionCollectionFields) return null;

      return this.$lodash.find(this.junctionCollectionFields, { primary_key: true });
    },
    doneLoading() {
      return this.relatedCollectionFields !== null && this.junctionCollectionFields !== null;
    }
  },
  created() {
    this.sort.field = this.visibleFields[0];
    this.selection = this.value.map(val => val.movie.id);

    this.getRelatedCollectionsFieldInfo();
  },
  methods: {
    getRelatedCollectionsFieldInfo() {
      const { junction_collection, collection } = this.relationship;

      if (!junction_collection || !collection) return null;

      this.loading = true;

      Promise.all([
        this.$api.getFields(junction_collection),
        this.$api.getFields(collection)
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
    saveSelection(selection) {
      this.selectionSaving = true;

      // Technically, the edit form only needs to know the IDs to be able to
      // save the relation, but the table itself needs the full data to be dis-
      // played.. I could potentially add a data-key that stores items' data
      // in case it wasn't populated in the value..
      //
      // Food for thought. Let's create the edit-item flow first, seeing that's
      // a bit easier. Good luck fixing this later! xoxo past Rijk
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
