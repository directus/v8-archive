<template>
  <div class="input-single-file">
    <v-card
      v-if="value"
      class="card"
      :title="value.title"
      :subtitle="subtitle"
      :src="src"
      :icon="icon"
      :href="href"
      :options="{
        remove: {
          text: $t('delete'),
          icon: 'delete'
        }
      }"
      @remove="$emit('input', null)"
    ></v-card>
    <v-upload
      v-else
      small
      :disabled="readonly"
      class="dropzone"
      @upload="saveUpload"
      :accept="options.accept"
      :multiple="false"
    ></v-upload>

    <v-button type="button" :disabled="readonly" @click="newFile = true">
      <i class="material-icons">add</i>{{ $t("new_file") }} </v-button
    ><!--
    --><v-button type="button" :disabled="readonly" @click="existing = true">
      <i class="material-icons">playlist_add</i>{{ $t("existing") }}
    </v-button>

    <portal to="modal" v-if="newFile">
      <v-modal :title="$t('file_upload')" @close="newFile = false">
        <div class="body">
          <v-upload @upload="saveUpload" :accept="options.accept" :multiple="false"></v-upload>
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
          collection="directus_files"
          :view-type="viewType"
          :selection="value ? [value] : []"
          :filters="filters"
          :view-query="viewQuery"
          :view-options="viewOptions"
          @options="setViewOptions"
          @query="setViewQuery"
          @select="$emit('input', $event[$event.length - 1])"
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
    subtitle() {
      if (!this.value) return "";

      return (
        this.value.filename.split(".").pop() +
        " • " +
        this.$d(new Date(this.value.uploaded_on), "short")
      );
    },
    src() {
      return this.value.type && this.value.type.startsWith("image")
        ? this.value.data.full_url
        : null;
    },
    icon() {
      return this.value.type && !this.value.type.startsWith("image")
        ? getIcon(this.value.type)
        : null;
    },
    href() {
      return this.value.type && this.value.type === "application/pdf"
        ? this.value.data.full_url
        : null;
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
      this.newFile = false;
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
