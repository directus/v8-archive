<template>
  <div class="interface-wysiwyg-container">
    <div ref="editor" class="interface-wysiwyg"></div>
  </div>
</template>

<script>
import Quill from "quill";
import "quill/dist/quill.core.css";
import "./quill.theme.css";

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
      this.$refs.editor.innerHTML = this.value;

      this.editor = new Quill(this.$refs.editor, {
        theme: "snow",
        modules: {
          toolbar: this.options.toolbarOptions
        }
      });

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
