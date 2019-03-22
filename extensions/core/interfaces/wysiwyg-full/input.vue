<template>
  <div
    class="interface-wysiwyg-container editor"
    :id="name"
    :name="name"
    @input="$emit('input', $event.target.innerHTML)"
  >
    <div class="editor__inner" :class="{ hidden: showSource }">
      <editor-menu-bar
        v-show="!showSource"
        :editor="editor"
        v-if="this.options"
      >
        <div
          class="menubar"
          slot-scope="{ commands, isActive }"
          :class="{ 'options-is-open': isActive.table() }"
        >
          <button
            v-if="options.toolbarOptions.includes('Bold')"
            class="menubar__button"
            :class="{ 'is-active': isActive.bold() }"
            @click="commands.bold"
            :style="{ order: options.toolbarOptions.indexOf('Bold') }"
          >
            <icon name="format_bold" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('Italic')"
            class="menubar__button"
            :class="{ 'is-active': isActive.italic() }"
            @click="commands.italic"
            :style="{ order: options.toolbarOptions.indexOf('Italic') }"
          >
            <icon name="format_italic" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('Strike')"
            class="menubar__button"
            :class="{ 'is-active': isActive.strike() }"
            @click="commands.strike"
            :style="{ order: options.toolbarOptions.indexOf('Strike') }"
          >
            <icon name="format_strikethrough" />
          </button>

          <button
            class="menubar__button"
            v-if="options.toolbarOptions.includes('Underline')"
            :class="{ 'is-active': isActive.underline() }"
            @click="commands.underline"
            :style="{ order: options.toolbarOptions.indexOf('Underline') }"
          >
            <icon name="format_underline" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('Code')"
            class="menubar__button"
            :class="{ 'is-active': isActive.code() }"
            @click="commands.code"
            :style="{ order: options.toolbarOptions.indexOf('Code') }"
          >
            <icon name="code" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('Paragraph')"
            class="menubar__button"
            :class="{ 'is-active': isActive.paragraph() }"
            @click="commands.paragraph"
            :style="{ order: options.toolbarOptions.indexOf('Paragraph') }"
          >
            <icon name="subject" />
          </button>

          <button
            v-show="options.toolbarOptions.includes('h1')"
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 1 }) }"
            @click="commands.heading({ level: 1 })"
            :style="{ order: options.toolbarOptions.indexOf('h1') }"
          >
            <span class="label">H1</span>
            <icon name="crop_square" />
          </button>

          <button
            v-show="options.toolbarOptions.includes('h2')"
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 2 }) }"
            @click="commands.heading({ level: 2 })"
            :style="{ order: options.toolbarOptions.indexOf('h2') }"
          >
            <span class="label">H2</span>
            <icon name="crop_square" />
          </button>

          <button
            v-show="options.toolbarOptions.includes('h3')"
            class="menubar__button"
            :class="{ 'is-active': isActive.heading({ level: 3 }) }"
            @click="commands.heading({ level: 3 })"
            :style="{ order: options.toolbarOptions.indexOf('h3') }"
          >
            <span class="label">H3</span>
            <icon name="crop_square" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('BulletList')"
            class="menubar__button"
            :class="{ 'is-active': isActive.bullet_list() }"
            @click="commands.bullet_list"
            :style="{ order: options.toolbarOptions.indexOf('BulletList') }"
          >
            <icon name="format_list_bulleted" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('OrderedList')"
            class="menubar__button"
            :class="{ 'is-active': isActive.ordered_list() }"
            @click="commands.ordered_list"
            :style="{ order: options.toolbarOptions.indexOf('OrderedList') }"
          >
            <icon name="format_list_numbered" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('Blockquote')"
            class="menubar__button"
            :class="{ 'is-active': isActive.blockquote() }"
            @click="commands.blockquote"
            :style="{ order: options.toolbarOptions.indexOf('Blockquote') }"
          >
            <icon name="format_quote" />
          </button>

          <button
            v-if="options.toolbarOptions.includes('Link')"
            class="menubar__button"
            :class="{ 'is-active': linkBubble }"
            @click="linkBubble = !linkBubble"
            :style="{ order: options.toolbarOptions.indexOf('Link') }"
          >
            <icon name="link" />
          </button>
          <button
            v-if="options.toolbarOptions.includes('Image')"
            class="menubar__button"
            @click="chooseImage = !chooseImage"
            :style="{ order: options.toolbarOptions.indexOf('Image') }"
          >
            <icon name="image" />
          </button>
          <button
            v-if="options.toolbarOptions.includes('Table')"
            class="menubar__button"
            :style="{ order: options.toolbarOptions.indexOf('Table') }"
            @click="
              commands.createTable({
                rowsCount: 3,
                colsCount: 3,
                withHeaderRow: false
              })
            "
          >
            <icon name="table_chart" />
          </button>
          <div
            class="options-fixed"
            v-if="isActive.table()"
            :class="{ 'is-open': isActive.table() }"
          >
            <button class="menubar__button" @click="commands.deleteTable">
              <span class="sup remove">
                <icon name="remove_circle" />
              </span>
              <icon name="table_chart" />
            </button>
            <button
              title="Insert before column"
              class="menubar__button"
              @click="commands.addColumnBefore"
            >
              <span class="sup add">
                <icon name="add_circle" />
              </span>
              <icon name="tab" />
            </button>
            <button
              title="Insert after column"
              class="menubar__button"
              @click="commands.addColumnAfter"
            >
              <span class="sup add">
                <icon name="add_circle" />
              </span>
              <icon name="tab" />
            </button>
            <button
              title="Delete column"
              class="menubar__button"
              @click="commands.deleteColumn"
            >
              <span class="sup remove">
                <icon name="remove_circle" />
              </span>
              <icon name="tab" />
            </button>
            <button
              title="Add row before"
              class="menubar__button"
              @click="commands.addRowBefore"
            >
              <span class="sup add">
                <icon name="add_circle" />
              </span>
              <icon name="border_top" />
            </button>
            <button class="menubar__button" @click="commands.addRowAfter">
              <span class="sup add">
                <icon name="add_circle" />
              </span>
              <icon name="border_bottom" />
            </button>
            <button class="menubar__button" @click="commands.deleteRow">
              <span class="sup remove">
                <icon name="remove_circle" />
              </span>
              <icon name="border_horizontal" />
            </button>
            <button class="menubar__button" @click="commands.toggleCellMerge">
              <icon name="merge_type" />
            </button>
          </div>
          <button
            v-if="options.toolbarOptions.includes('HorizontalRule')"
            :style="{ order: options.toolbarOptions.indexOf('HorizontalRule') }"
            class="menubar__button"
            @click="commands.horizontal_rule"
          >
            <icon name="maximize" />
          </button>
          <div
            class="bottom__actions"
            v-if="options.toolbarOptions.includes('History')"
            :style="{ order: options.toolbarOptions.indexOf('HorizontalRule') }"
          >
            <button class="menubar__button" @click="commands.undo">
              <icon name="undo" />
            </button>
            <button class="menubar__button" @click="commands.redo">
              <icon name="redo" />
            </button>
          </div>
        </div>
      </editor-menu-bar>

      <editor-menu-bubble
        class="menububble"
        :class="{ visible: linkBubble }"
        :editor="editor"
        @hide="hideLinkMenu"
      >
        <div
          slot-scope="{ commands, isActive, getMarkAttrs, menu }"
          class="menububble__item"
          :class="{ 'is-active': menu.isActive }"
          :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`"
        >
          <form
            :linkBubbleclass="'menububble__form'"
            v-if="linkMenuIsActive"
            @submit.prevent="setLinkUrl(commands.link, linkUrl)"
          >
            <input
              class="menububble__input"
              type="text"
              v-model="linkUrl"
              placeholder="https://"
              ref="linkInput"
              @keydown.esc="hideLinkMenu"
            />
            <button
              class="menububble__button"
              @click="setLinkUrl(commands.link, null)"
              type="button"
            >
              <icon name="close" />
            </button>
          </form>

          <template v-else>
            <button
              class="menububble__button"
              @click="showLinkMenu(getMarkAttrs('link'))"
              :class="{ 'is-active': isActive.link() }"
            >
              <span>{{
                isActive.link() ? $t("edit_link") : $t("add_link")
              }}</span>
              <icon name="link" />
            </button>
          </template>
        </div>
      </editor-menu-bubble>

      <editor-content
        ref="editor"
        :class="['interface-wysiwyg', readonly ? 'readonly' : '']"
        class="editor__content"
        :editor="editor"
      />
    </div>
    <!--- edit raw html in hidden text area --->
    <div class="editor__raw" v-show="showSource">
      <v-textarea
        v-if="showSource && showRaw"
        v-model.lazy="editorText"
        class="textarea"
        :id="name"
        :value="editor.view.dom.innerHTML"
        :placeholder="options.placeholder"
        :rows="options.rows ? +options.rows : 10"
      ></v-textarea>

      <!--- show formatted raw html in CodeMirror --->
      <CodeMirror
        :placeholder="options.placeholder"
        class="textarea"
        v-if="showSource && !showRaw"
        :alt-options="options.codeMirrorOptions"
        :value="editor.view.dom.innerHTML"
        v-model.lazy="editorText"
        :name="'htmlmixed'"
        type="textarea"
      >
      </CodeMirror>

      <div
        class="editor__rawformat"
        v-if="showSource"
        @click="showRaw = !showRaw"
      >
        <span
          :style="{ color: !showRaw ? 'var(--accent)' : 'var(--light-gray)' }"
          >formatted</span
        >
        |
        <span
          :style="{ color: showRaw ? 'var(--accent)' : 'var(--light-gray)' }"
          >unformatted</span
        >
      </div>
    </div>
    <p
      class="editor__button"
      @click="updateText(editor.view.dom.innerHTML)"
      v-html="showSource ? 'Show WYSIWYG' : 'Source Code'"
    ></p>

    <!--- modal for image selection --->

    <portal to="modal" v-if="chooseImage">
      <v-modal
        ref="imageModal"
        :title="$t('choose_one')"
        :buttons="{
          done: {
            text: $t('done'),
            disabled: !imageUrlRaw || (imageUrlRaw && imageUrlRawBroken)
          }
        }"
        @close="chooseImage = false"
        @done="insertImageUrl(imageUrlRaw)"
      >
        <div
          class="interface-wysiwyg-modal-url-input"
          :class="{ 'is-active': imageUrlRaw }"
        >
          <v-input
            v-model="imageUrlRaw"
            placeholder="Paste url to image or select an existing"
            @input="imageUrlRawBroken = false"
          ></v-input>
          <div class="interface-wysiwyg-modal-url-preview" v-if="imageUrlRaw">
            <i v-if="imageUrlRawBroken" class="material-icons error icon"
              >broken_image</i
            >
            <img
              v-else
              :src="imageUrlRaw"
              alt="preview-url-image"
              class="image"
              @error="imageUrlRawBroken = true"
            />
          </div>
        </div>
        <v-items
          v-if="imageUrlRaw === ''"
          collection="directus_files"
          view-type="cards"
          :selection="[]"
          :view-options="viewOptions"
          @select="insertItem($event[0])"
        >
        </v-items>
      </v-modal>
    </portal>
  </div>
</template>
<script>
import Icon from "./components/icon";
import mixin from "../../../mixins/interface";
import { Editor, EditorContent, EditorMenuBar, EditorMenuBubble } from "tiptap";
import {
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  HorizontalRule,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Strike,
  Underline,
  History,
  Image,
  Table,
  TableHeader,
  TableRow,
  TableCell
} from "tiptap-extensions";

import CodeMirror from "../code/input";

export default {
  name: "interface-wysiwyg",
  mixins: [mixin],
  watch: {
    value(newVal) {
      if (newVal && !this.showSource) {
        this.editorText = newVal;
      } else {
        this.$emit("input", this.editorText);
      }
    }
  },

  methods: {
    init() {
      const ext = [
        new Blockquote(),
        new BulletList(),
        new CodeBlock(),
        new HardBreak(),
        new Heading({ levels: [1, 2, 3] }),
        new HorizontalRule(),
        new ListItem(),
        new OrderedList(),
        new TodoItem(),
        new TodoList(),
        new Bold(),
        new Image(),
        new Code(),
        new Italic(),
        new Link(),
        new Strike(),
        new Underline(),
        new History(),
        new Table(),
        new TableHeader(),
        new TableCell(),
        new TableRow()
      ];

      this.editor = new Editor({
        extensions: ext,
        content: ""
      });

      if (this.value) {
        this.editor.setContent(this.value);
      }
    },

    updateText($text) {
      if (!!this.showSource) {
        this.editor.setContent(this.editorText);
      } else {
        this.editorText = $text;
      }
      this.showSource = !this.showSource;
    },

    addImageCommand(data) {
      if (data.command !== null || data.command !== "data") {
        this.editor.commands.image({
          src: data
        });

        this.chooseImage = false;
      }
    },

    insertItem(item) {
      let url = item.data.full_url;
      if (this.options.custom_url) {
        url = `${this.options.custom_url}${item.filename}`;
      }
      // @todo implement image surce base url
      // const index = (this.editor.getSelection() || {}).index || this.editor.getLength();
      this.addImageCommand(url);
    },

    insertImageUrl(url) {
      if (url !== "") {
        console.log(url);
        this.chooseImage = false;
        this.addImageCommand(url);
      }
    },

    showLinkMenu(attrs) {
      this.linkUrl = attrs.href;
      this.linkMenuIsActive = true;
      this.$nextTick(() => {
        this.$refs.linkInput.focus();
      });
    },
    hideLinkMenu() {
      this.linkUrl = null;
      this.linkMenuIsActive = false;
    },
    setLinkUrl(command, url) {
      command({ href: url });
      this.hideLinkMenu();
      this.editor.focus();
    },

    destroy() {
      this.editor.destroy();
    }
  },
  components: {
    EditorMenuBubble,
    EditorContent,
    EditorMenuBar,
    Icon,
    CodeMirror
  },

  data() {
    return {
      editorText: "",
      editor: null,
      showSource: false,
      showRaw: false,
      showTableOptions: false,
      chooseExisting: false,
      chooseImage: false,
      newFile: false,
      linkBubble: false,
      linkUrl: null,
      linkMenuIsActive: false,
      imageUrlRaw: "",
      imageUrlRawBroken: false,
      viewOptions: {
        title: "title",
        subtitle: "type",
        content: "description",
        src: "data"
      },
      lineCount: 0,
      codeMirrorOptions: {}
    };
  },

  mounted() {
    this.init();
    console.log(this.options);
  },
  beforeDestroy() {
    this.editor.destroy();
  }
};
</script>

<style lang="scss">
@import "assets/scss/editor";
</style>
