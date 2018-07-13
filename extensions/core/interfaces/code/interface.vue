<template>
  <div class="interface-code">
    <codemirror ref="cm"
                :value="options.placeholder"
                :options="cmOptions"
                @ready="onCmReady"
                @focus="onCmFocus"
                @input="onCmCodeChange"
    ></codemirror>
    <div class="clipboard-btn">
      <button class="btn" @click="$emit('input', code)">
        <i class="material-icons">assignment_turned_in</i>
      </button>
    </div>
  </div>
</template>

<script>

// require component
import { codemirror } from 'vue-codemirror';

// require styles
import 'codemirror/lib/codemirror.css'

// language
import 'codemirror/mode/javascript/javascript.js'

// theme css
// import 'codemirror/theme/monokai.css'

// require active-line.js
import'codemirror/addon/selection/active-line.js'

// styleSelectedText - (styleSelectedText and css class .CodeMirror-selectedtext)
import'codemirror/addon/selection/mark-selection.js'
import'codemirror/addon/search/searchcursor.js'

// hint - (extraKeys and hintOptions)
import'codemirror/addon/hint/show-hint.js'
import'codemirror/addon/hint/show-hint.css'
import'codemirror/addon/hint/javascript-hint.js'
import'codemirror/addon/selection/active-line.js'

// highlightSelectionMatches - (highlightSelectionMatches)
import'codemirror/addon/scroll/annotatescrollbar.js'
import'codemirror/addon/search/matchesonscrollbar.js'
import'codemirror/addon/search/searchcursor.js'
import'codemirror/addon/search/match-highlighter.js'

// keyMap - (matchBrackets)
import'codemirror/mode/clike/clike.js'
import'codemirror/addon/edit/matchbrackets.js'
import'codemirror/addon/comment/comment.js'
import'codemirror/addon/dialog/dialog.js'
import'codemirror/addon/dialog/dialog.css'
import'codemirror/addon/search/searchcursor.js'
import'codemirror/addon/search/search.js'
import'codemirror/keymap/sublime.js'

// foldGutter - (foldGutter and gutters)
import'codemirror/addon/fold/foldgutter.css'
import'codemirror/addon/fold/brace-fold.js'
import'codemirror/addon/fold/comment-fold.js'
import'codemirror/addon/fold/foldcode.js'
import'codemirror/addon/fold/foldgutter.js'
import'codemirror/addon/fold/indent-fold.js'
import'codemirror/addon/fold/markdown-fold.js'
import'codemirror/addon/fold/xml-fold.js'

import './code.css';
import mixin from '../../../mixins/interface';

export default {
  name: "interface-code",
  mixins: [mixin],
  data () {
    return {
      code: "",   
    }
  },
  watch: {
    options: function () {
      this.codemirror.setSize("100%", this.options.height);
    }
  },
  computed: {
    cmOptions() {
      return {
        tabSize: 4,
        indentUnit: 4,
        styleActiveLine: true,
        lineNumbers: this.options.lineNumber,
        readOnly: this.readonly? "nocursor" : false,
        styleSelectedText: true,
        line: true,
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
        highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
        mode: 'text/javascript',
        // hint.js options
        hintOptions:{
          // Automatically complete when there is only one match
          completeSingle: true
        },
        // Shortcuts Available in three modes sublime、emacs、vim
        keyMap: "sublime",
        matchBrackets: true,
        showCursorWhenSelecting: true,
        theme: "default",
        extraKeys: { "Ctrl": "autocomplete" }
      }
    },
    codemirror() {
      return this.$refs.cm.codemirror;
    }
  },
  methods: {
    onCmReady(cm) {
      console.log('the editor is readied!', cm)
    },
    onCmFocus(cm) {
      console.log('the editor is focus!', cm)
    },
    onCmCodeChange(newCode) {
      this.code = newCode;
    }
  },
  components: {
    codemirror,
  },
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
</style>
