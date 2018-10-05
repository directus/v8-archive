<template>
  <div class="input-single-file">
    <v-card
      v-if="value"
      class="card"
      :title="value.title"
      :subtitle="subtitle"
      :src="value.data.full_url"
      :options="{
        remove: {
          text: $t('delete'),
          icon: 'delete'
        }
      }"
      @remove="$emit('input', null)"></v-card>
    <v-upload v-else small class="dropzone" @upload="saveUpload"></v-upload>

    <v-button type="button" @click="newFile = true">
      <i class="material-icons">add</i>{{ $t('new_file') }}
    </v-button><!--

 --><v-button type="button" @click="existing = true">
      <i class="material-icons">playlist_add</i>{{ $t('existing') }}
    </v-button>

    <portal to="modal" v-if="newFile">
      <v-modal :title="$t('file_upload')" @close="newFile = false">
        <div class="body">
          <v-upload @upload="saveUpload"></v-upload>
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
        action-required
        @done="existing = false">
        <v-items
          collection="directus_files"
          :view-type="viewType"
          :selection="selection"
          :filters="filters"
          :view-query="viewQuery"
          :view-options="viewOptions"
          @options="setViewOptions"
          @query="setViewQuery"
          @select="selection = [$event[$event.length - 1]]"></v-items>
      </v-modal>
    </portal>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      newFile: false,
      existing: false,
      selection: [],

      viewOptionsOverride: {},
      viewTypeOverride: null,
      viewQueryOverride: {},
      filtersOverride: []
    };
  },
  computed: {
    subtitle() {
      if (!this.value) return "";

      return (
        this.value.filename.split(".").pop() +
        " • " +
        this.$d(new Date(this.value.uploaded_on), "short")
      );
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
    saveUpload(fileInfo) {
      this.$emit("input", fileInfo.data);
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
  },
  watch: {
    selection(newVal) {
      const id = newVal[0];

      if (id == null) {
        this.$emit("input", null);
        return;
      }

      this.$api
        .getItem("directus_files", newVal)
        .then(res => res.data)
        .then(file => {
          this.$emit("input", file);
        })
        .catch(error => {
          this.$events.emit("error", {
            notify: this.$t("something_went_wrong_body"),
            error
          });
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.card,
.dropzone {
  margin-bottom: 20px;
  width: 100%;
  max-width: var(--width-x-large);
}

.dropzone {
  height: 190px;
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
