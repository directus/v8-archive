<template>
  <div ref="editor" class="interface-wysiwyg">{{ value }}</div>
</template>

<script>
import MediumEditor from "medium-editor";
import "medium-editor/dist/css/medium-editor.css";

import mixin from "../../../mixins/interface";

export default {
  name: "interface-wysiwyg",
  mixins: [mixin],
  computed: {
    editorOptions() {
      return {
        placeholder: false,
        toolbar: {
          buttons: this.options.buttons
        }
      };
    }
  },
  mounted() {
    this.init();
  },
  beforeDestroy() {
    this.destroy();
  },
  watch: {
    options() {
      this.destroy();
      this.init();
    },
    value(newVal) {
      if (newVal !== this.editor.origElements.innerHTML) {
        this.editor.origElements.innerHTML = newVal;
      }
    }
  },
  methods: {
    init() {
      this.editor = new MediumEditor(this.$refs.editor, this.editorOptions);
      this.editor.origElements.addEventListener("input", () => {
        this.$emit("input", this.editor.origElements.innerHTML);
      });
    },
    destroy() {
      this.editor.destroy();
    }
  }
};
</script>

<style lang="scss">
.interface-wysiwyg {
  width: 100%;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  color: var(--gray);
  padding: 12px 15px;
  font-weight: 400;
  line-height: 1.8;
  transition: var(--fast) var(--transition);
  transition-property: color, border-color, padding;
  background-color: var(--white);
  min-height: 200px;
  max-height: 1000px;
  overflow: scroll;

  &::placeholder {
    color: var(--light-gray);
  }

  &:focus {
    color: var(--darker-gray);
    border-color: var(--accent);
    outline: 0;
  }

  &:focus + i {
    color: var(--accent);
  }

  &:-webkit-autofill {
    box-shadow: inset 0 0 0 1000px var(--white) !important;
    color: var(--dark-gray) !important;
    -webkit-text-fill-color: var(--dark-gray) !important;
  }

  &:-webkit-autofill,
  &:-webkit-autofill:hover,
  &:-webkit-autofill:focus {
    border: var(--input-border-width) solid var(--lighter-gray);
    background-color: var(--white);
    box-shadow: inset 0 0 0 2000px var(--white);
  }

  b,
  strong {
    font-weight: 700;
  }

  a {
    color: var(--accent);
  }

  p {
    padding: 10px 0;
  }

  blockquote {
    border-left: 4px solid var(--lighter-gray);
    margin-bottom: 5px;
    margin-top: 5px;
    padding-left: 16px;
  }

  h1 {
    font-size: 2em;
    margin: 25px 0 15px;
  }
  h2 {
    font-size: 1.5em;
    margin: 20px 0 12px;
  }
  h3 {
    font-size: 1.17em;
    margin: 18px 0 10px;
  }
  h4 {
    font-size: 1em;
    margin: 16px 0 10px;
  }
  h5 {
    font-size: 0.83em;
    margin: 15px 0 10px;
  }
  h6 {
    font-size: 0.67em;
    margin: 5px 0 10px;
  }
}

.medium-toolbar-arrow-under:after {
  top: 60px;
  border-color: var(--gray) transparent transparent transparent;
}

.medium-toolbar-arrow-over:before {
  top: -8px;
  border-color: transparent transparent var(--gray) transparent;
}

.medium-editor-toolbar {
  background-color: var(--gray);
  border-radius: var(--border-radius);
}

.medium-editor-toolbar li {
  padding: 0;
}

.medium-editor-toolbar li button {
  min-width: 60px;
  height: 60px;
  border: none;
  border-right: 1px solid var(--light-gray);
  background-color: transparent;
  color: var(--white);
  transition: background-color var(--fast) var(--transition),
    color var(--fast) var(--transition);
}

.medium-editor-toolbar li button:hover {
  background-color: var(--darker-gray);
  color: var(--white);
}

.medium-editor-toolbar li .medium-editor-button-active {
  background-color: var(--darker-gray);
  color: var(--white);
}

.medium-editor-toolbar li .medium-editor-button-first {
  border-radius: var(--border-radius) 0 0 var(--border-radius);
}

.medium-editor-toolbar li .medium-editor-button-last {
  border-right: none;
  border-radius: 0 var(--border-radius) var(--border-radius) 0;
}

.medium-editor-toolbar-form .medium-editor-toolbar-input {
  height: 60px;
  background: var(--gray);
  color: var(--white);
  padding-left: 20px;
}

.medium-editor-toolbar-form
  .medium-editor-toolbar-input::-webkit-input-placeholder {
  color: var(--white);
  color: rgba(255, 255, 255, 0.8);
}

.medium-editor-toolbar-form .medium-editor-toolbar-input:-moz-placeholder {
  /* Firefox 18- */
  color: var(--white);
}

.medium-editor-toolbar-form .medium-editor-toolbar-input::-moz-placeholder {
  /* Firefox 19+ */
  color: var(--white);
}

.medium-editor-toolbar-form .medium-editor-toolbar-input:-ms-input-placeholder {
  color: var(--white);
}

.medium-editor-toolbar-form a {
  color: var(--white);
}

.medium-editor-toolbar-anchor-preview {
  background: var(--gray);
  color: var(--white);
  border-radius: var(--border-radius);
}

.medium-editor-toolbar-anchor-preview a {
  padding: 0px 6px;
}

.medium-editor-placeholder:after {
  color: #9ccea6;
}
</style>
