function _objectWithoutProperties(e, t) { if (null == e) return {}; var o, r, i = _objectWithoutPropertiesLoose(e, t); if (Object.getOwnPropertySymbols) { var n = Object.getOwnPropertySymbols(e); for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]); } return i; }
function _objectWithoutPropertiesLoose(r, e) { if (null == r) return {}; var t = {}; for (var n in r) if ({}.hasOwnProperty.call(r, n)) { if (-1 !== e.indexOf(n)) continue; t[n] = r[n]; } return t; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == typeof i ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != typeof t || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != typeof i) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
import { Controller } from '@hotwired/stimulus';
import EditorJS from '@editorjs/editorjs';
var BUILTIN_TOOLS = {
  header: () => import('@editorjs/header'),
  list: () => import('@editorjs/list'),
  paragraph: () => import('@editorjs/paragraph'),
  image: () => import('@editorjs/image'),
  code: () => import('@editorjs/code'),
  delimiter: () => import('@editorjs/delimiter'),
  quote: () => import('@editorjs/quote'),
  warning: () => import('@editorjs/warning'),
  table: () => import('@editorjs/table'),
  embed: () => import('@editorjs/embed'),
  marker: () => import('@editorjs/marker'),
  inlineCode: () => import('@editorjs/inline-code'),
  checklist: () => import('@editorjs/checklist'),
  linkTool: () => import('@editorjs/link'),
  raw: () => import('@editorjs/raw'),
  underline: () => import('@editorjs/underline')
};
export default class _Class extends Controller {
  constructor() {
    super(...arguments);
    _defineProperty(this, "editorInstance", null);
  }
  connect() {
    var _this = this;
    return _asyncToGenerator(function* () {
      if (_this.editorInstance) {
        return;
      }
      var tools = yield _this.resolveTools();
      var config = _this.buildConfig(tools);
      _this.dispatchEvent('options', config);
      var {
        maxWidth,
        minHeight: minHeightOpt,
        border
      } = _this.extraOptionsValue;
      if (maxWidth || typeof minHeightOpt === 'string' || border) {
        _this.applyStyles(maxWidth, typeof minHeightOpt === 'string' ? minHeightOpt : null, border);
      }
      _this.editorInstance = new EditorJS(_objectSpread(_objectSpread({}, config), {}, {
        onReady: () => {
          _this.dispatchEvent('connect', _this.editorInstance);
        },
        onChange: function () {
          var _onChange = _asyncToGenerator(function* () {
            yield _this.syncData();
          });
          function onChange() {
            return _onChange.apply(this, arguments);
          }
          return onChange;
        }()
      }));
    })();
  }
  disconnect() {
    if (this.editorInstance) {
      this.editorInstance.destroy();
      this.editorInstance = null;
    }
  }
  resolveTools() {
    var _this2 = this;
    return _asyncToGenerator(function* () {
      var toolEntries = Object.entries(_this2.toolsValue);
      var resolved = {};
      yield Promise.all(toolEntries.map(/*#__PURE__*/function () {
        var _ref2 = _asyncToGenerator(function* (_ref) {
          var _entry$config;
          var [name, entry] = _ref;
          var config = (_entry$config = entry.config) !== null && _entry$config !== void 0 ? _entry$config : {};
          var packageName = entry.package;
          try {
            var toolClass;
            if (packageName) {
              var _module$default;
              // Custom tool: dynamic import from the specified npm package
              var module = yield import(/* webpackIgnore: true */packageName);
              toolClass = (_module$default = module.default) !== null && _module$default !== void 0 ? _module$default : module[Object.keys(module)[0]];
            } else if (BUILTIN_TOOLS[name]) {
              // Built-in tool
              var _module = yield BUILTIN_TOOLS[name]();
              toolClass = _module.default;
            } else {
              console.warn("Editor.js tool \"".concat(name, "\" is not recognized. Register it via the \"editorjs:options\" event or provide a \"package\" name."));
              return;
            }
            resolved[name] = _objectSpread(_objectSpread({
              class: toolClass
            }, Object.keys(config).length > 0 ? {
              config
            } : {}), entry.tunes ? {
              tunes: entry.tunes
            } : {});
          } catch (e) {
            console.warn("Editor.js tool \"".concat(name, "\" could not be loaded:"), e);
          }
        });
        return function (_x) {
          return _ref2.apply(this, arguments);
        };
      }()));

      // Post-resolution: inject EditorJS library and sibling tools for nested editors (e.g. columns)
      for (var [name, tool] of Object.entries(resolved)) {
        var _tool$config;
        if ((_tool$config = tool.config) !== null && _tool$config !== void 0 && _tool$config.requireEditorJS) {
          delete tool.config.requireEditorJS;
          tool.config.EditorJsLibrary = EditorJS;
          // Pass all other resolved tools so nested editors can use them
          var {
              [name]: _
            } = resolved,
            nestedTools = _objectWithoutProperties(resolved, [name].map(_toPropertyKey));
          tool.config.tools = nestedTools;
        }
      }
      return resolved;
    })();
  }
  buildConfig(tools) {
    var {
      placeholder,
      minHeight,
      readOnly,
      autofocus,
      inlineToolbar
    } = this.extraOptionsValue;
    var initialData;
    var rawValue = this.inputTarget.value;
    if (rawValue) {
      try {
        initialData = JSON.parse(rawValue);
      } catch (_unused) {
        // Not valid JSON, ignore
      }
    }
    var config = {
      holder: this.editorContainerTarget,
      tools,
      data: initialData,
      placeholder: placeholder !== null && placeholder !== void 0 ? placeholder : 'Start writing...',
      minHeight: typeof minHeight === 'number' ? minHeight : 200,
      readOnly: readOnly !== null && readOnly !== void 0 ? readOnly : false,
      autofocus: autofocus !== null && autofocus !== void 0 ? autofocus : false,
      inlineToolbar: inlineToolbar !== null && inlineToolbar !== void 0 ? inlineToolbar : true
    };
    if (this.tunesValue.length > 0) {
      config.tunes = this.tunesValue;
    }
    return config;
  }
  toCssValue(value) {
    return typeof value === 'number' ? "".concat(value, "px") : value;
  }
  applyStyles(maxWidth, minHeight, border) {
    var scopeId = "editorjs-".concat(Math.random().toString(36).slice(2, 9));
    this.editorContainerTarget.setAttribute('data-editorjs-scope', scopeId);
    var scope = "[data-editorjs-scope=\"".concat(scopeId, "\"]");
    var css = '';
    if (maxWidth) {
      var val = this.toCssValue(maxWidth);
      css += "".concat(scope, " .ce-toolbar__content, ").concat(scope, " .ce-block__content { max-width: ").concat(val, "; }\n");
    }
    if (minHeight) {
      css += "".concat(scope, " .ce-redactor { min-height: ").concat(minHeight, "; }\n");
    }
    if (border) {
      var borderValue = typeof border === 'string' ? border : '1px solid #e0e0e0';
      css += "".concat(scope, " { border: ").concat(borderValue, "; border-radius: 4px; padding: 8px; }\n");
    }
    if (css) {
      var style = document.createElement('style');
      style.textContent = css;
      this.editorContainerTarget.appendChild(style);
    }
  }
  syncData() {
    var _this3 = this;
    return _asyncToGenerator(function* () {
      if (!_this3.editorInstance) {
        return;
      }
      try {
        var outputData = yield _this3.editorInstance.save();
        _this3.inputTarget.value = JSON.stringify(outputData);
        _this3.inputTarget.dispatchEvent(new Event('input', {
          bubbles: true
        }));
        _this3.inputTarget.dispatchEvent(new Event('change', {
          bubbles: true
        }));
        _this3.dispatchEvent('change', outputData);
      } catch (e) {
        console.error('Editor.js save failed:', e);
      }
    })();
  }
  dispatchEvent(name) {
    var payload = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    this.dispatch(name, {
      detail: payload,
      prefix: 'editorjs'
    });
  }
}
_defineProperty(_Class, "targets", ['input', 'editorContainer']);
_defineProperty(_Class, "values", {
  tools: {
    type: Object,
    default: {}
  },
  extraOptions: {
    type: Object,
    default: {}
  },
  tunes: {
    type: Array,
    default: []
  }
});