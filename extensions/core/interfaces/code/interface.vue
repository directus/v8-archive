<template>
  <div class="interface-code" v-bind:class="{ inactive: readonly }">
    <codemirror ref="cm"
      :value="code"
      :options="cmOptions"
      @input="onCmInput"
    ></codemirror>
    <div class="clipboard-btn">
      <button class="btn" @click="onBtnClick" v-tooltip="$t('interfaces-code-fill_placeholder')">
        <i class="material-icons">assignment_turned_in</i>
      </button>
    </div>
    <div class="line-count">
      <p>{{ line_counts }}&nbsp;lines&nbsp;of&nbsp;<span class="lang">{{ lang_type }}</span></p>
    </div>
  </div>
</template>

<script>
import { codemirror } from "vue-codemirror";
import "codemirror/lib/codemirror.css";

// languages
import "codemirror/mode/javascript/javascript.js";
import "codemirror/mode/vue/vue.js";
import "codemirror/mode/php/php.js";

// import active-line.js
import "codemirror/addon/selection/active-line.js";

// styleSelectedText - (styleSelectedText and css class .CodeMirror-selectedtext)
import "codemirror/addon/selection/mark-selection.js";
import "codemirror/addon/search/searchcursor.js";

// hint - (extraKeys and hintOptions)
import "codemirror/addon/hint/show-hint.js";
import "codemirror/addon/hint/show-hint.css";
import "codemirror/addon/hint/javascript-hint.js";
import "codemirror/addon/selection/active-line.js";

// highlightSelectionMatches - (highlightSelectionMatches)
import "codemirror/addon/scroll/annotatescrollbar.js";
import "codemirror/addon/search/matchesonscrollbar.js";
import "codemirror/addon/search/searchcursor.js";
import "codemirror/addon/search/match-highlighter.js";

// keyMap - (matchBrackets)
import "codemirror/mode/clike/clike.js";
import "codemirror/addon/edit/matchbrackets.js";
import "codemirror/addon/comment/comment.js";
import "codemirror/addon/dialog/dialog.js";
import "codemirror/addon/dialog/dialog.css";
import "codemirror/addon/search/searchcursor.js";
import "codemirror/addon/search/search.js";
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
      line_counts: 0,
      lang_type: null,
      avail_types: {
        "text/javascript": "javascript",
        "application/json": "json",
        "text/x-vue": "vue",
        "application/x-httpd-php": "php"
      },
      code: null
    };
  },
  mounted() {
    this.setEditorSize(this.codemirror, this.options);
    this.line_counts = this.codemirror.lineCount();
    this.lang_type = this.avail_types[this.options.mode];
  },
  watch: {
    options: function(options) {
      this.setEditorSize(this.codemirror, options);
      this.codemirror.setOption("mode", options.mode);
      this.lang_type = this.avail_types[options.mode];
    }
  },
  computed: {
    codemirror() {
      return this.$refs.cm.codemirror;
    },
    cmOptions() {
      return {
        tabSize: 4,
        indentUnit: 4,
        styleActiveLine: true,
        lineNumbers: this.options.lineNumber,
        readOnly: this.readonly ? "nocursor" : false,
        styleSelectedText: true,
        line: true,
        highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
        mode: this.options.mode,
        // hint.js options
        hintOptions: {
          // Automatically complete when there is only one match
          completeSingle: true
        },
        // Shortcuts Available in three modes sublime、emacs、vim
        keyMap: "sublime",
        matchBrackets: true,
        showCursorWhenSelecting: true,
        theme: "default",
        extraKeys: { Ctrl: "autocomplete" }
      };
    }
  },
  methods: {
    onCmInput(newCode) {
      // Set the height of the code editor
      this.setEditorSize(this.codemirror, this.options);
      // Get line counts of the code editor
      this.line_counts = this.codemirror.lineCount();
      this.$emit("input", newCode);
    },
    onBtnClick() {
      this.code = this.options.placeholder;
    },
    setEditorSize(cm, opts) {
      if (opts.max != null || opts.min != null) {
        cm.setSize("100%", "auto");
        let max = opts.max,
          min = opts.min,
          height = cm.getWrapperElement().offsetHeight;
        if (min > height) {
          cm.setSize("100%", min);
        } else if (max < height) {
          cm.setSize("100%", max);
        } else {
          cm.setSize("100%", "auto");
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-code {
  position: relative;
  max-width: var(--width-large);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  font-size: 13px;
}
.clipboard-btn {
  position: absolute;
  top: 5px;
  right: 20px;
  z-index: 10;
}
.btn {
  border: none;
  color: var(--lighter-gray);
  font-size: 24px;
  cursor: pointer;
}
.btn:hover {
  color: var(--gray);
}
.btn:active {
  color: var(--lighter-gray);
}
.line-count {
  position: absolute;
  right: 5px;
  bottom: -20px;
}
.line-count > * {
  color: var(--light-gray);
  font-style: italic;
}
.lang {
  font-weight: 700;
  text-transform: uppercase;
}
</style>
