<template>
  <input ref="file" type="file" class="filepond">
</template>

<script>
import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileEncode from 'filepond-plugin-file-encode';

import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';

import mixin from '../../../mixins/interface';

FilePond.registerPlugin(
  FilePondPluginImagePreview,
  FilePondPluginFileEncode,
);

export default {
  mixins: [mixin],
  mounted() {
    this.pond = FilePond.create(this.$refs.file);
    document.addEventListener('FilePond:encoded', this.processFile);
  },
  beforeDestroy() {
    this.pond.destroy();
  },
  methods: {
    processFile(e) {
      if (this.options.nameField) {
        this.$emit('setfield', {
          field: this.options.nameField,
          value: e.detail.name,
        });
      }

      if (this.options.sizeField) {
        this.$emit('setfield', {
          field: this.options.sizeField,
          value: e.detail.size,
        });
      }

      if (this.options.typeField) {
        this.$emit('setfield', {
          field: this.options.typeField,
          value: e.detail.type,
        });
      }

      this.$emit('input', e.detail.data);
    },
  },
};
</script>
