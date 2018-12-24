<template>
  <div :class="{ inactive: readonly }" class="interface-code">
    <codemirror
      ref="codemirrorEl"
      :options="cmOptions"
      :value="stringValue"
      @input="onInput"
    ></codemirror>

    <button
      v-if="options.template"
      v-tooltip="$t('interfaces-code-fill_template')"
      @click="fillTemplate"
    >
      <i class="material-icons">playlist_add</i>
    </button>

    <small class="line-count">
      {{
        $tc("interfaces-code-loc", lineCount, {
          count: lineCount,
          lang: language
        })
      }}
    </small>
  </div>
</template>

<script>
import { codemirror } from "vue-codemirror";

import "codemirror/lib/codemirror.css";

import "codemirror/mode/vue/vue.js";
import "codemirror/mode/javascript/javascript.js";
import "codemirror/mode/php/php.js";

import "codemirror/addon/selection/active-line.js";
import "codemirror/addon/selection/mark-selection.js";
import "codemirror/addon/search/searchcursor.js";
import "codemirror/addon/hint/show-hint.js";
import "codemirror/addon/hint/show-hint.css";
import "codemirror/addon/hint/javascript-hint.js";
import "codemirror/addon/selection/active-line.js";
import "codemirror/addon/scroll/annotatescrollbar.js";
import "codemirror/addon/search/matchesonscrollbar.js";
import "codemirror/addon/search/searchcursor.js";
import "codemirror/addon/search/match-highlighter.js";
import "codemirror/addon/edit/matchbrackets.js";
import "codemirror/addon/comment/comment.js";
import "codemirror/addon/dialog/dialog.js";
import "codemirror/addon/dialog/dialog.css";
import "codemirror/addon/search/searchcursor.js";
import "codemirror/addon/search/search.js";

import "codemirror/addon/display/autorefresh.js";

import "codemirror/keymap/sublime.js";

import "./code.css";

import mixin from "../../../mixins/interface";

export default {
  name: "interface-code",
  mixins: [mixin],
  components: {
    codemirror
  },
  data() {
    return {
      lineCount: 0,

      cmOptions: {
        tabSize: 4,
        autoRefresh: true,
        indentUnit: 4,
        styleActiveLine: true,
        lineNumbers: this.options.lineNumber,
        readOnly: this.readonly ? "nocursor" : false,
        styleSelectedText: true,
        line: true,
        highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
        mode: this.mode,
        hintOptions: {
          completeSingle: true
        },
        keyMap: "sublime",
        matchBrackets: true,
        showCursorWhenSelecting: true,
        theme: "default",
        extraKeys: { Ctrl: "autocomplete" }
      }
    };
  },
  mounted() {
    const { codemirror } = this.$refs.codemirrorEl;
    this.lineCount = codemirror.lineCount();
  },
  watch: {
    options(newVal, oldVal) {
      if (newVal.language !== oldVal.language) {
        this.$set(this.cmOptions, "mode", newVal.language);
      }

      if (newVal.lineNumber !== oldVal.lineNumber) {
        this.$set(this.cmOptions, "lineNumbers", newVal.lineNumber);
      }
    }
  },
  computed: {
    availableTypes() {
      return {
        "text/plain": "Plain Text",
        "text/javascript": "JavaScript",
        "application/json": "JSON",
        "text/x-vue": "Vue",
        "application/x-httpd-php": "PHP"
      };
    },
    language() {
      return this.availableTypes[this.options.language];
    },
    stringValue() {
      if (this.value == null) return null;

      if (typeof this.value === "object") {
        return JSON.stringify(this.value, null, 4);
      }

      return this.value;
    },
    mode() {
      // There is no dedicated mode for JSON in codemirror. Switch to JS mode when JSON is selected
      return this.options.language === "application/json"
        ? "text/javascript"
        : this.options.language;
    }
  },
  methods: {
    onInput(value) {
      const { codemirror } = this.$refs.codemirrorEl;

      if (this.lineCount !== codemirror.lineCount()) {
        this.lineCount = codemirror.lineCount();
      }

      if (this.options.language === "application/json") {
        try {
          this.$emit("input", JSON.parse(value));
        } catch (e) {
          // silently ignore saving value if it's not valid json
        }
      } else {
        this.$emit("input", value);
      }
    },
    fillTemplate() {
      if (this.$lodash.isObject(this.options.template)) {
        return this.$emit(
          "input",
          JSON.stringify(this.options.template, null, 4)
        );
      }

      if (this.options.language === "application/json") {
        try {
          this.$emit("input", JSON.parse(this.options.template));
        } catch (e) {
          // silently ignore saving value if it's not valid json
        }
      } else {
        this.$emit("input", this.options.template);
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-code {
  position: relative;
  width: 100%;
  max-width: var(--width-x-large);
  font-size: 13px;

  &:focus {
    border-color: var(--accent);
  }
}

small {
  position: absolute;
  right: 0;
  bottom: -20px;
  font-style: italic;
  text-align: right;
  color: var(--light-gray);
}

button {
  position: absolute;
  top: 10px;
  right: 10px;
  user-select: none;
  color: var(--light-gray);
  cursor: pointer;
  transition: color var(--fast) var(--transition-out);
  z-index: 10;

  &:hover {
    transition: none;
    color: var(--dark-gray);
  }
}
</style>
