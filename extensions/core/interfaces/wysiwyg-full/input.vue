<template>
  <div class="interface-wysiwyg-container">
    <div ref="editor" class="interface-wysiwyg"></div>
  </div>
</template>

<script>
import Quill from "quill";
import "quill/dist/quill.core.css";
import "./quill.theme.css";
import { ImageUpload } from 'quill-image-upload';

Quill.register('modules/imageUpload', ImageUpload);

import mixin from "../../../mixins/interface";

export default {
  name: "interface-wysiwyg",
  mixins: [mixin],
  mounted() {
    this.init();
  },
  watch: {
    value(newVal) {
      if (newVal !== this.editor.root.innerHTML) {
        this.editor.clipboard.dangerouslyPasteHTML(this.value);
      }
    }
  },
  methods: {
    init() {
      let uploadURL = ''
      if (this.options.upload_files) uploadURL = `${this.$store.state.auth.url}/${this.$store.state.auth.project}/files`;
      this.editor = new Quill(this.$refs.editor, {
        theme: "snow",
        modules: {
          toolbar: this.options.toolbarOptions,
          imageUpload: {
              url: uploadURL, // server url. If the url is empty then the base64 returns
              method: 'POST', // change query method, default 'POST'
              name: 'image', // custom form name
              withCredentials: false, // withCredentials
              headers: {
                Authorization: `Bearer ${this.$store.state.auth.token}`
              }, // add custom headers, example { token: 'your-token'}
              csrf: { token: 'token', hash: '' }, // add custom CSRF
              customUploader: null, // add custom uploader
              // personalize successful callback and call next function to insert new url to the editor
              callbackOK: (serverResponse, next) => {
                this.$store.dispatch("loadingFinished", 'uploadingFile');
                if(typeof serverResponse === 'string') return next(serverResponse);
                // pass image url to editor
                next(serverResponse.data.data.full_url);
              },
              // personalize failed callback
              callbackKO: serverError => {
                alert(serverError);
              },
              // optional
              // add callback when a image have been chosen
              checkBeforeSend: (file, next) => {
                this.$store.dispatch("loadingStart", {
                  id: 'uploadingFile'
                });
                next(file); // go back to component and send to the server
              }
            }
          }
      });

      this.editor.clipboard.dangerouslyPasteHTML(this.value);

      this.editor.on("text-change", () => {
        this.$emit("input", this.editor.root.innerHTML);
      });
    }
  }
};
</script>

<style lang="scss">
.interface-wysiwyg-container {
  max-width: var(--width-x-large);
}
</style>
