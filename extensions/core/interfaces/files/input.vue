<template>
  <div class="input-single-file">
    <div v-if="value" class="preview">
      <v-card
        v-for="file in files"
        class="card"
        :title="file.title"
        :subtitle="file.subtitle"
        :src="file.src"
        :icon="file.icon"
        :href="file.href"
        :options="{
          remove: {
            text: $t('delete'),
            icon: 'delete'
          }
        }"
        @remove="deleteFile(file[relatedPrimaryKeyField.field])"
      ></v-card>
    </div>
    <v-button type="button" :disabled="readonly" @click="newFile = true">
      <i class="material-icons">add</i>{{ $t("new_file") }} </v-button
    ><!--
    --><v-button type="button" :disabled="readonly" @click="existing = true">
      <i class="material-icons">playlist_add</i>{{ $t("existing") }}
    </v-button>

    <portal to="modal" v-if="newFile">
      <v-modal :title="$t('file_upload')" @close="newFile = false">
        <div class="body">
          <v-upload @upload="saveUpload" :multiple="true"></v-upload>
        </div>
      </v-modal>
    </portal>

    <portal to="modal" v-if="existing">
      <v-modal
        :title="$t('choose_one')"
        :buttons="{
          done: {
            text: $t('done')
          }
        }"
        @close="existing = false"
        @done="existing = false"
      >
        <v-items
          :collection="relation.junction.collection_one.collection"
          :view-type="viewType"
          :selection="selection"
          :filters="filters"
          :view-query="viewQuery"
          :view-options="viewOptions"
          @options="setViewOptions"
          @query="setViewQuery"
          @select="selectItems"
        ></v-items>
      </v-modal>
    </portal>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import getIcon from "./get-icon";

export default {
  mixins: [mixin],
  data() {
    return {
      newFile: false,
      existing: false,

      viewOptionsOverride: {},
      viewTypeOverride: null,
      viewQueryOverride: {},
      filtersOverride: []
    };
  },
  computed: {
    /*
     * This interface is using a many to many relationship. In order to retrieve
     * and save (to the junction table) we need to know in what key to store
     * the (new / edited) file
     */
    junctionFieldName() {
      return this.relation.junction.field_many.field;
    },

    /*
     * Converts the junction collection rows into formatted file objects that
     * can be used to render the card previews.
     *
     * Also, filter out the values that are marked for deletion
     */
    files() {
      if (!this.value) return [];

      return this.value
        .filter(jr => !jr.$delete)
        .map(relation => {
        const file = relation[this.junctionFieldName];
        return {
          id: file[this.relatedPrimaryKeyField.field],
          title: file.title,
          subtitle: file.filename.split(".").pop() + " • " + this.$d(new Date(file.uploaded_on), "short"),
          src: file.type && file.type.startsWith("image")
            ? file.data.thumbnails[0].url
            : null,
          icon: file.type && !file.type.startsWith("image")
            ? getIcon(file.type)
            : null,
          href: file.type && file.type === "application/pdf"
            ? file.data.full_url
            : null
        };
      });
    },

    /*
     * The primary key of the related collection. Most likely going to be `id` in
     * directus_files, but seeing that the user can theoretically change this,
     * we need to make sure we use the value dynamically
     */
    relatedPrimaryKeyField() {
      return this.$lodash.find(this.relation.junction.collection_one.fields, { primary_key: true });
    },

    /*
     * The value of the interface is an array of rows from the junction table. The
     * listing view expects the actual items that are selected, so we have to return
     * just those. Also, we need to filter out the items that are already marked
     * for deletion from the selection, otherwise the listing view doesn't know
     * the user already de-selected it
     */
    selection() {
      if (!this.value) return [];

      return this.value
        .filter(jr => !jr.$delete)
        .map(jr => jr[this.junctionFieldName]);
    },

    viewOptions() {
      const viewOptions = this.options.viewOptions;
      return {
        ...viewOptions,
        ...this.viewOptionsOverride
      };
    },

    viewType() {
      if (this.viewTypeOverride) return this.viewTypeOverride;
      return this.options.viewType;
    },

    viewQuery() {
      const viewQuery = this.options.viewQuery;
      return {
        ...viewQuery,
        ...this.viewQueryOverride
      };
    },

    filters() {
      return [...this.options.filters, ...this.filtersOverride];
    }
  },
  methods: {
    /*
     * The values are being stored in the junction table. In order to add a new
     * file, we need to save the new file's info inside the junction row under
     * the correct key
     */
    saveUpload(fileInfo) {
      this.$emit("input", [...this.value, {
        [this.junctionFieldName]: fileInfo.data
      }]);

      this.newFile = false;
    },

    /*
     * Selecting items is a little bit more involved. If the user selects a file
     * that wasn't selected before, we need to add it into the junction record
     * nested under the correct field key name (which is accessible in
     * junctionFieldName). If the user de-selects an item, instead of removing
     * the row from the value, we need to add the $delete flag to the item. That
     * way, Directus knows to delete the value
     */
    selectItems(newSelection) {
      // this.value is an array of the junction collection rows
      const currentValue = (this.value || []);
      const currentSelection = currentValue.map(jr => jr[this.junctionFieldName]);

      const relatedPrimaryKeyFieldName = this.relatedPrimaryKeyField.field;

      const currentSelectionIDs = currentSelection.map(file => file[relatedPrimaryKeyFieldName]);
      const newSelectionIDs = newSelection.map(file => file[relatedPrimaryKeyFieldName]);

      // We need to merge both the selections where the current selected files
      // that aren't selected anymore get the $delete flag. Files that are selected
      // that weren't selected before need to be added as file object.
      const deletedFileIDs = currentSelectionIDs.filter(id => newSelectionIDs.includes(id) === false);

      const junctionRowsForDeletedFiles = currentValue
        .filter(jr => deletedFileIDs.includes(jr[this.junctionFieldName][relatedPrimaryKeyFieldName]))
        .map(jr => ({
          ...jr,
          $delete: true
        }));

      // Finally, we have to convert the array of selected items into an array
      // of junction table records
      const junctionRows = newSelection.map(s => {
        return {
          [this.junctionFieldName]: s
        };
      });

      this.$emit("input", [...junctionRowsForDeletedFiles, ...junctionRows]);
    },

    /*
     * Fired when the user clicks the delete icon in the card preview of a file
     * Should add the delete flag to the junction row
     */
    deleteFile(id) {
      const newValue = this.value.map(junctionRow => {
        const file = junctionRow[this.junctionFieldName];

        if (file[this.relatedPrimaryKeyField.field] === id) {
          return {
            ...junctionRow,
            $delete: true
          };
        }

        return junctionRow;
      });

      this.$emit("input", newValue);
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
.preview {
  display: flex;
  margin-bottom: 20px;
  flex-wrap: wrap;

  .card {
    margin-right: 20px;
    margin-bottom: 20px;
  }
}

button {
  display: inline-block;
  margin-left: 20px;
  &:first-of-type {
    margin-left: 0;
  }
}

.body {
  padding: 20px;
}
</style>
