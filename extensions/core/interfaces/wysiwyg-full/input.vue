<template>
  <div class="interface-wysiwyg-container">
    <div ref="editor" class="interface-wysiwyg"></div>
    <portal to="modal" v-if="chooseExisting">
      <v-modal
        :title="$t('choose_one')"
        :buttons="{
          done: {
            text: $t('done')
          }
        }"
        @close="chooseExisting = false"
        @done="chooseExisting = false"
      >
        <v-items
          collection="directus_files"
          view-type="cards"
          :selection="[]"
          :view-options="viewOptions"
          @select="insertItem($event[0])"
        ></v-items>
      </v-modal>
    </portal>
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
  data() {
    return {
      chooseExisting: false,
      viewOptions: {title:"title",subtitle:"type",content:"description",src:"data"}
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
                // pass image url to editor
                if (typeof serverResponse === 'string') return next(serverResponse);
                else if (this.options.custom_url) return next(`${this.options.custom_url}${serverResponse.data.filename}`);
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

      // Make custom icons for image buttons
      const customButton = document.querySelector('.ql-choose-existing');
      if(customButton) {
        customButton.className += " material-icons icon";
        customButton.addEventListener('click', evt => this.openModal());
      }
      const imageButton = document.querySelector('.ql-image');
      if(imageButton) {
        imageButton.innerHTML = ""
        imageButton.className += " material-icons icon";
      }
    },
    openModal() {
        this.chooseExisting = true
    },
    insertItem(image) {
      let url = image.data.full_url
      if(this.options.custom_url) url = `${this.options.custom_url}${image.filename}`
      const index = (this.editor.getSelection() || {}).index || this.editor.getLength();
      this.editor.insertEmbed(index, 'image', url, 'user');
      this.chooseExisting = false
    }
  }
};
</script>

<style lang="scss">
.interface-wysiwyg-container {
  max-width: var(--width-x-large);
}
.material-icons {
  font-size: 20px;
}
.ql-choose-existing {
  padding: 3px 5px;
  color:  var(--light-gray);
  &:hover {
    color: var(--accent);
  }
  &:after {
    content: "collections";
    font-size: 20px;
  }
}

.ql-image {
  padding: 3px 5px;
  color:  var(--light-gray);
  &:hover {
    color: var(--accent);
  }
  &:after {
    content: "add_photo_alternate";
    font-size: 20px;
  }
}
</style>
