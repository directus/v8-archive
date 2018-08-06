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
      @remove="$emit('input', null)" />

    <button class="style-btn" type="button" @click="newFile = true">
      <i class="material-icons">add</i>{{ $t('new') }}
    </button>

    <button class="style-btn" type="button" @click="existing = true">
      <i class="material-icons">playlist_add</i>{{ $t('select_existing') }}
    </button>

    <portal to="modal" v-if="newFile">
      <v-modal :title="$t('file_upload')" @close="newFile = false">
        <div class="body">
          <v-upload @upload="saveUpload" />
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
          @select="selection = [$event[$event.length - 1]]" />
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
      return this.value.filename.split('.').pop() + " • " + this.$d(new Date(this.value.upload_date), "short");
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
      return [
        ...this.options.filters,
        ...this.filtersOverride
      ];
    },
  },
  methods: {
    saveUpload(fileInfo) {
      this.$emit("input", fileInfo.res.data);
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

      this.$api.getItem("directus_files", newVal)
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
.card {
  margin-bottom: 20px;
}

button {
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

.body {
  padding: 20px;
}
</style>
