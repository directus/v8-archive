/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__meta__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__meta___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__meta__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__mixin__ = __webpack_require__(4);
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["a"] = ({
  mixins: [Object(__WEBPACK_IMPORTED_MODULE_1__mixin__["a" /* default */])(__WEBPACK_IMPORTED_MODULE_0__meta___default.a)],
  data() {
    return {
      valid: null
    };
  },
  methods: {
    updateValue(value) {
      if (this.options.onlyAllowValidJson) {
        let valid = false;

        try {
          value = JSON.stringify(JSON.parse(value));
          valid = true;
        } catch (err) {}

        this.valid = valid;

        if (valid || value.length === 0) {
          this.$emit('input', value);
        }
      } else {
        this.$emit('input', value);
      }
    },
    process(event) {
      const textarea = event.target;
      const value = textarea.value;

      const caret = textarea.selectionStart;
      const before = value.substr(0, caret);
      const after = value.substr(caret);
      const lastChar = before.trim().slice(-1);
      const nextChar = after.substr(0, 1);

      if (event.key === 'Enter') {
        const previousLine = this.getPreviousLine(value, before);
        const indents = this.getIndents(previousLine);

        let diff = nextChar === '}' ? -1 : 0;

        if (lastChar === '{' || lastChar === '[') {
          diff = nextChar === '}' || nextChar === ']' ? 0 : 1;
          this.addIndent(before, after, indents + diff);
        }

        if (indents + diff > 0) {
          this.addIndent(before, after, indents + diff);
        }

        event.preventDefault();
      }

      if (event.key === '}' || event.key === ']') {
        this.removeIndent(before, after);
      }
    },
    getPreviousLine(value, before) {
      const lines = value.split(/\n/g);
      const line = before.trimRight().split(/\n/g).length - 1;
      return lines[line] || '';
    },
    getIndents(line) {
      const indent = this.options.indent;
      const regex = new RegExp(`^(${indent}+)`, 'g');
      const match = line.match(regex);
      return match && match[0].length / indent.length || 0;
    },
    addIndent(before, after, num) {
      if (!num) return;

      const textarea = this.$refs.textarea;
      const indent = this.options.indent;
      const newValue = before + '\n' + indent.repeat(num) + after;

      textarea.value = newValue;
      this.lastValue = newValue;

      const selection = newValue.length - after.length;
      textarea.selectionStart = selection;
      textarea.selectionEnd = selection;
    },
    removeIndent(before, after) {
      const textarea = this.$refs.textarea;
      const indent = this.options.indent;
      const remove = before.slice(before.length - indent.length, before.length);

      if (remove !== indent) return;

      const newValue = before.slice(0, -indent.length) + after;
      const selection = before.length - indent.length;

      textarea.value = newValue;
      this.lastValue = newValue;
      textarea.selectionStart = selection;
      textarea.selectionEnd = selection;
    }
  }
});

/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_build_node_modules_vue_loader_lib_selector_type_script_index_0_Interface_vue__ = __webpack_require__(0);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__build_node_modules_vue_loader_lib_template_compiler_index_id_data_v_be831318_hasScoped_false_buble_transforms_build_node_modules_vue_loader_lib_selector_type_template_index_0_Interface_vue__ = __webpack_require__(5);
var disposed = false
var normalizeComponent = __webpack_require__(2)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_build_node_modules_vue_loader_lib_selector_type_script_index_0_Interface_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__build_node_modules_vue_loader_lib_template_compiler_index_id_data_v_be831318_hasScoped_false_buble_transforms_build_node_modules_vue_loader_lib_selector_type_template_index_0_Interface_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "interfaces/src/json/Interface.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-be831318", Component.options)
  } else {
    hotAPI.reload("data-v-be831318", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 2 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 3 */
/***/ (function(module, exports) {

module.exports = {"name":"JSON","version":"1.0.0","dataTypes":{"TEXT":null,"TINYTEXT":null,"MEDIUMTEXT":null,"LONGTEXT":null,"VARCHAR":255},"options":{"placeholder":{"interface":"text-input","comment":"Enter placeholder text","length":200},"indent":{"interface":"text-input","comment":"What character(s) to use as indentation","defaultValue":"\t"},"onlyAllowValidJson":{"interface":"toggle","comment":"Only allow valid JSON to be saved to the DB","defaultValue":true}}}

/***/ }),
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = mixin;
function mixin(meta) {
  const defaultValues = {};

  Object.keys(meta.options).forEach(key => {
    defaultValues[key] = meta.options[key].defaultValue;
  });

  return {
    created() {
      this.$lodash.forIn(meta.translations, (messages, locale) => {
        this.$i18n.mergeLocaleMessage(locale, messages);
      });
    },
    props: {
      name: {
        type: String,
        required: true
      },
      value: {
        type: null,
        default: null
      },
      type: {
        type: String,
        default: Object.keys(meta.dataTypes)[0]
      },
      length: {
        type: [String, Number],
        default: Object.values(meta.dataTypes)[0]
      },
      readonly: {
        type: Boolean,
        default: false
      },
      required: {
        type: Boolean,
        default: false
      },
      _options: {
        type: Object,
        default: () => ({})
      }
    },
    data() {
      const options = Object.assign({}, defaultValues, this._options);

      return {
        options
      };
    },
    watch: {
      _options: {
        handler() {
          this.options = Object.assign({}, defaultValues, this._options);
        },
        deep: true
      }
    }
  };
}

/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "textarea",
    {
      ref: "textarea",
      attrs: { readonly: _vm.readonly, placeholder: _vm.options.placeholder },
      on: {
        keydown: _vm.process,
        input: function($event) {
          _vm.updateValue($event.target.value)
        }
      }
    },
    [_vm._v(_vm._s(_vm.value) + "\n")]
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-be831318", esExports)
  }
}

/***/ })
/******/ ]);