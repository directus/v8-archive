<template>
  <div class="interface-file-upload">
    <input
      v-if="!value"
      ref="file" type="file" class="filepond">
    <div v-else>
      {{ value }}
    </div>
  </div>
</template>

<script>
import * as FilePond from "filepond";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileEncode from "filepond-plugin-file-encode";

import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

import mixin from "../../../mixins/interface";

FilePond.registerPlugin(FilePondPlwuginImagePreview, FilePondPluginFileEncode);

export default {
    mixins: [mixin],
    mounted() {
        this.pond = FilePond.create(this.$refs.file);
        this.pond.on("addfile", this.processFile);
    },
    beforeDestroy() {
        this.pond.destroy();
    },
    methods: {
        processFile() {
            const filePondInput = document.querySelector(
                '.interface-file-upload input[type="hidden"][name="filepond"]'
            );

            const fileInfo = JSON.parse(filePondInput.value);

            if (this.options.nameField) {
                this.$emit("setfield", {
                    field: this.options.nameField,
                    value: e.detail.name
                });
            }

            if (this.options.sizeField) {
                this.$emit("setfield", {
                    field: this.options.sizeField,
                    value: e.detail.size
                });
            }

            if (this.options.typeField) {
                this.$emit("setfield", {
                    field: this.options.typeField,
                    value: e.detail.type
                });
            }

            this.$emit("input", fileInfo.data);
        }
    }
};
</script>
