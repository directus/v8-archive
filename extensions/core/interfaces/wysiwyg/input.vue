<template>
  <div ref="input" :class="[{ fullscreen: distractionFree }, 'interface-wysiwyg-container']">
    <div ref="editor" class="interface-wysiwyg"></div>
    <button
      v-on:click='distractionFree = !distractionFree'
      type="button"
      class="fullscreen-toggle"
      v-tooltip="$t('interfaces-wysiwyg-distraction_free_mode')">
      <i class="material-icons">{{fullscreenIcon}}</i>
    </button>
  </div>
</template>

<script>
import MediumEditor from "medium-editor";
import "medium-editor/dist/css/medium-editor.css";

import mixin from "../../../mixins/interface";

export default {
  name: "interface-wysiwyg",
  mixins: [mixin],
  data() {
    return {
      distractionFree: false
    };
  },
  computed: {
    editorOptions() {
      return {
        placeholder: false,
        toolbar: {
          buttons: this.options.buttons
        }
      };
    },
    fullscreenIcon() {
      return this.distractionFree ? "close" : "fullscreen";
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
      if (newVal !== this.editor.getContent()) {
        this.editor.setContent(newVal);
      }
    },
    distractionFree(on) {
      if (on) {
        this.$helpers.disableBodyScroll(this.$refs.input);
      } else {
        this.$helpers.enableBodyScroll(this.$refs.input);
      }
    }
  },
  methods: {
    init() {
      this.editor = new MediumEditor(this.$refs.editor, this.editorOptions);

      if (this.value) {
        this.editor.setContent(this.value);
      }

      this.editor.origElements.addEventListener("input", () => {
        this.$emit("input", this.editor.getContent());
      });
    },
    destroy() {
      this.editor.destroy();
    }
  }
};
</script>

<style lang="scss">
.interface-wysiwyg-container {
  position: relative;
  max-width: var(--width-large);

  &.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    max-width: 100%;
    max-height: 100%;
    background-color: var(--body-background);

    button.fullscreen-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 101;
      background-color: var(--darker-gray);
      color: var(--white);
      &:hover {
        background-color: var(--darkest-gray);
      }
    }

    .interface-wysiwyg {
      color: var(--dark-gray);

      border: none;
      border-radius: 0;
      padding: 80px 80px 100px 80px;
      max-width: 880px;
      margin: 0 auto;
      height: 100%;
      max-height: 100%;

      font-size: 21px;
      line-height: 33px;
      font-weight: 400;

      p {
        margin-top: 30px;
      }

      blockquote {
        margin-top: 30px;
        padding-left: 20px;
      }

      h1 {
        margin-top: 60px;
      }

      h2 {
        margin-top: 60px;
      }

      h3 {
        margin-top: 40px;
      }

      h4 {
        margin-top: 30px;
      }

      h5 {
        margin-top: 20px;
      }

      h6 {
        margin-top: 20px;
      }
    }
  }
}

button.fullscreen-toggle {
  position: absolute;
  top: 10px;
  right: 10px;
  background-color: var(--white);
  color: var(--lighter-gray);
  border-radius: 100%;
  padding: 4px;
  &:hover {
    color: var(--dark-gray);
  }
}

.interface-wysiwyg {
  position: relative;
  width: 100%;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  color: var(--gray);
  padding: 12px 15px;
  transition: var(--fast) var(--transition);
  transition-property: color, border-color, padding;
  background-color: var(--white);
  min-height: 300px;
  max-height: 800px;
  overflow: scroll;
  font-weight: 400;
  line-height: 1.7em;

  & > :first-child {
    padding-top: 0;
    margin-top: 0;
  }

  &::placeholder {
    color: var(--light-gray);
  }

  &:hover {
    transition: none;
    border-color: var(--light-gray);
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
    margin-top: 20px;
  }

  blockquote {
    border-left: 4px solid var(--lightest-gray);
    font-style: italic;
    margin-top: 20px;
    padding-left: 20px;
  }

  pre {
    max-width: 100%;
    background-color: var(--body-background);
    padding: 20px 10px;
    font-family: "Roboto Mono", mono;
    overflow: scroll;
    margin-top: 20px;
  }

  h1 {
    font-size: 3em;
    line-height: 1.2em;
    font-weight: 600;
    margin-top: 30px;
  }
  h2 {
    font-size: 2.5em;
    line-height: 1.2em;
    font-weight: 600;
    margin-top: 30px;
  }
  h3 {
    font-size: 2em;
    line-height: 1.2em;
    font-weight: 600;
    margin-top: 30px;
  }
  h4 {
    font-size: 1.87em;
    line-height: 1.2em;
    font-weight: 600;
    margin-top: 20px;
  }
  h5 {
    font-size: 1.5em;
    line-height: 1.2em;
    font-weight: 600;
    margin-top: 20px;
  }
  h6 {
    font-size: 1.2em;
    line-height: 1.2em;
    font-weight: 600;
    margin-top: 20px;
  }
}

.medium-toolbar-arrow-under:after {
  top: 40px;
  border-color: var(--darker-gray) transparent transparent transparent;
}

.medium-toolbar-arrow-over:before {
  top: -8px;
  border-color: transparent transparent var(--darker-gray) transparent;
}

.medium-editor-toolbar {
  background-color: var(--darker-gray);
  border-radius: var(--border-radius);
}

.medium-editor-toolbar li {
  padding: 0;
}

.medium-editor-toolbar li button {
  min-width: 40px;
  height: 40px;
  line-height: 0;
  border: none;
  border-right: 1px solid var(--dark-gray);
  background-color: transparent;
  color: var(--white);
  transition: background-color var(--fast) var(--transition),
    color var(--fast) var(--transition);
}

.medium-editor-toolbar li button:hover {
  background-color: var(--dark-gray);
  color: var(--white);
}

.medium-editor-toolbar li .medium-editor-button-active {
  background-color: var(--dark-gray);
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
  height: 40px;
  background: var(--darker-gray);
  border-right: 1px solid var(--gray);
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
