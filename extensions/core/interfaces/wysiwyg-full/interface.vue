w<template>
  <div ref="editor" class="interface-wysiwyg">{{ value }}</div>
</template>

<script>
import Quill from "quill";
import "quill/dist/quill.core.css";
import "./quill.theme.css";

import mixin from "../../../mixins/interface";

export default {
  name: "interface-wysiwyg",
  mixins: [mixin],
  computed: {
    editorOptions() {
      const { inline, block, embeds } = this.options;
      return {
        theme: "snow",
        modules: {
          toolbar: [[...inline], [...block], [...embeds]]
        },
        formats: [...inline, ...block, ...embeds]
      };
    }
  },
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
      this.editor = new Quill(this.$refs.editor, this.editorOptions);
      this.editor.on("text-change", () => {
        this.$emit("input", this.editor.root.innerHTML);
      });
    }
  }
};
</script>
