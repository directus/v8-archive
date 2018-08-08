<template>
  <div class="interface-markdown">
    <v-textarea
      v-show="editor"
      class="textarea"
      :value="value"
      @input="$emit('input', $event)"
      :id="name" />
    <div class="preview" v-show="!editor" v-html="compiledMarkdown"></div>
    <button
      @click="editor = !editor"
      v-tooltip="tooltipText"><i class="material-icons">{{ editor ? 'remove_red_eye' : 'code' }}</i></button>
  </div>
</template>

<script>
import marked from "marked";
import mixin from "../../../mixins/interface";

export default {
  data() {
    return {
      editor: true
    };
  },
  computed: {
    compiledMarkdown() {
      if (this.value) {
        return marked(this.value);
      }

      return this.value;
    },
    tooltipText() {
      let mode = this.editor ? "Preview" : "Editor";
      return "Show " + mode;
    }
  },
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.interface-markdown {
  max-width: var(--width-large);
  position: relative;
}

.textarea,
.preview {
  max-width: var(--width-large);
  min-height: 200px;
}

.textarea {
  font-family: "Roboto Mono", monospace;
  display: block;
}

button {
  position: absolute;
  top: 1em;
  right: 1em;

  i {
    color: var(--light-gray);
    transition: var(--fast) var(--transition);
  }

  &:hover,
  .user-is-tabbing &:focus {
    i {
      color: var(--primary);
    }
  }
}

.preview {
  background-color: var(--white);
  padding: 10px;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
}
</style>
