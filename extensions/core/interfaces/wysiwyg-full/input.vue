<template>
  <div
      class="interface-wysiwyg-container editor"
      :id="name"
      :name="name"
      @input="$emit('input', $event.target.innerHTML)"
  >
  <div class="editor__inner" :class="{'hidden':showSource}">
      <editor-menu-bar v-show="!showSource" :editor="editor">
        <div
            class="menubar"
            slot-scope="{ commands, isActive }"
            :class="{ 'options-is-open': isActive.table() }"
        >
          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.bold() }"
              @click="commands.bold"

          >
            <icon name="format_bold"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.italic() }"
              @click="commands.italic"
          >
            <icon name="format_italic"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.strike() }"
              @click="commands.strike"
          >
            <icon name="format_strikethrough"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.underline() }"
              @click="commands.underline"
          >
            <icon name="format_underline"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.code() }"
              @click="commands.code"
          >
            <icon name="code"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.paragraph() }"
              @click="commands.paragraph"
          >
            <icon name="subject"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.heading({ level: 1 }) }"
              @click="commands.heading({ level: 1 })"
          >
            <span class="label">H1</span>
            <icon name="crop_square"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.heading({ level: 2 }) }"
              @click="commands.heading({ level: 2 })"
          >
            <span class="label">H2</span>
            <icon name="crop_square"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.heading({ level: 3 }) }"
              @click="commands.heading({ level: 3 })"
          >
            <span class="label">H3</span>
            <icon name="crop_square"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.bullet_list() }"
              @click="commands.bullet_list"
          >
            <icon name="format_list_bulleted"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.ordered_list() }"
              @click="commands.ordered_list"
          >
            <icon name="format_list_numbered"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': isActive.blockquote() }"
              @click="commands.blockquote"
          >
            <icon name="format_quote"/>
          </button>

          <button
              class="menubar__button"
              :class="{ 'is-active': linkBubble }"
              @click="linkBubble = !linkBubble"
          >
            <icon name="link"/>
          </button>
          <button
              class="menubar__button"
              @click="chooseImage = !chooseImage"
          >
            <icon name="image"/>
          </button>
          <button
              class="menubar__button"
              @click="
              commands.createTable({
                rowsCount: 3,
                colsCount: 3,
                withHeaderRow: false
              })
            "
          >
            <icon name="table_chart"/>
          </button>
          <div
              class="options-fixed"
              v-if="isActive.table()"
              :class="{ 'is-open': isActive.table() }"
          >
            <button class="menubar__button" @click="commands.deleteTable">
              <span class="sup remove">
                <icon name="remove_circle"/>
              </span>
              <icon name="table_chart"/>
            </button>
            <button
                title="Insert before column"
                class="menubar__button"
                @click="commands.addColumnBefore"
            >
              <span class="sup add">
                <icon name="add_circle"/>
              </span>
              <icon name="tab"/>
            </button>
            <button
                title="Insert after column"
                class="menubar__button"
                @click="commands.addColumnAfter"
            >
              <span class="sup add">
                <icon name="add_circle"/>
              </span>
              <icon name="tab"/>
            </button>
            <button
                title="Delete column"
                class="menubar__button"
                @click="commands.deleteColumn"
            >
              <span class="sup remove">
                <icon name="remove_circle"/>
              </span>
              <icon name="tab"/>
            </button>
            <button
                title="Add row before"
                class="menubar__button"
                @click="commands.addRowBefore"
            >
              <span class="sup add">
                <icon name="add_circle"/>
              </span>
              <icon name="border_top"/>
            </button>
            <button class="menubar__button" @click="commands.addRowAfter">
              <span class="sup add">
                <icon name="add_circle"/>
              </span>
              <icon name="border_bottom"/>
            </button>
            <button class="menubar__button" @click="commands.deleteRow">
              <span class="sup remove">
                <icon name="remove_circle"/>
              </span>
              <icon name="border_horizontal"/>
            </button>
            <button class="menubar__button" @click="commands.toggleCellMerge">
              <icon name="merge_type"/>
            </button>
          </div>
          <button class="menubar__button" @click="commands.horizontal_rule">
            <icon name="maximize"/>
          </button>
          <div class="bottom__actions">
            <button
                class="menubar__button"
                @click="commands.undo"
            >
              <icon name="undo"/>
            </button>
            <button
                class="menubar__button"
                @click="commands.redo"
            >
              <icon name="redo"/>
            </button>
          </div>
        </div>
      </editor-menu-bar>

      <editor-menu-bubble class="menububble" :class="{'visible':linkBubble }" :editor="editor" @hide="hideLinkMenu">
        <div
            slot-scope="{ commands, isActive, getMarkAttrs, menu }"
            class="menububble__item"
            :class="{ 'is-active': menu.isActive }"
            :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`"
        >
          <form linkBubbleclass="menububble__form" v-if="linkMenuIsActive" @submit.prevent="setLinkUrl(commands.link, linkUrl)">
            <input class="menububble__input" type="text" v-model="linkUrl" placeholder="https://" ref="linkInput"
                   @keydown.esc="hideLinkMenu"/>
            <button class="menububble__button" @click="setLinkUrl(commands.link, null)" type="button">
              <icon name="close"/>
            </button>
          </form>

          <template v-else>
            <button
                class="menububble__button"
                @click="showLinkMenu(getMarkAttrs('link'))"
                :class="{ 'is-active': isActive.link() }"
            >
              <span>{{ isActive.link() ? $t('edit_link') : $t('add_link')}}</span>
              <icon name="link"/>
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
    <div class="editor__raw">
      <v-textarea
          v-if="showSource"
          v-model.lazy="editorText"
          class="textarea"
          :id="name"
          :value="editor.view.dom.innerHTML"
          :placeholder="options.placeholder"
          :rows="options.rows ? +options.rows : 10"
      ></v-textarea>
      <button
          @click="updateText(editor.view.dom.innerHTML)"
          v-html="showSource ? 'Show WYSIWYG' : 'Source Code'"
      ></button>
    </div>
    <!--- image modal for file selection --->
    <portal to="modal" v-if="chooseImage">
      <v-modal ref="imageModal"
               :title="$t('choose_one')"
               :buttons="{
          done: {
            text: $t('done'),
            disabled: !imageUrlRaw
          }
        }"
               @close="chooseImage = false"
               @done="insertImageUrl(imageUrlRaw)"
      >

        <div class="interface-wysiwyg-modal-url-input">
          <v-input
              v-model="imageUrlRaw"
              placeholder="Paste url to image or select an existing"
          ></v-input>
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
  import Icon from "./components/Icon";
  import {Editor, EditorContent, EditorMenuBar, EditorMenuBubble} from "tiptap";
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

  import mixin from "../../../mixins/interface";

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
        this.editor = new Editor({
          extensions: [
            new Blockquote(),
            new BulletList(),
            new CodeBlock(),
            new HardBreak(),
            new Heading({levels: [1, 2, 3]}),
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
          ],
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
          })

          this.chooseImage = false;
        }
      },

      insertItem(image) {
        let url = image.data.full_url;
        console.log(url)
        if (this.options.custom_url) {
          url = `${this.options.custom_url}${image.filename}`;
        }
        // const index =
        //   (this.editor.getSelection() || {}).index || this.editor.getLength();
        this.addImageCommand(url)
      },

      insertImageUrl(url) {
        if (url !== "") {
          this.chooseImage = false
          this.addImageCommand(src)
        }
      },

      showLinkMenu(attrs) {
        this.linkUrl = attrs.href
        this.linkMenuIsActive = true
        this.$nextTick(() => {
          this.$refs.linkInput.focus()
        })
      },
      hideLinkMenu() {
        this.linkUrl = null
        this.linkMenuIsActive = false
      },
      setLinkUrl(command, url) {
        command({href: url})
        this.hideLinkMenu()
        this.editor.focus()
      },

      destroy() {
        this.editor.destroy();
      }
    },
    components: {
      EditorMenuBubble,
      EditorContent,
      EditorMenuBar,
      Icon
    },

    data() {
      return {
        editorText: "",
        editor: null,
        showSource: false,
        showTableOptions: false,
        chooseExisting: false,
        chooseImage: false,
        newFile: false,
        linkBubble: false,
        linkUrl: null,
        linkMenuIsActive: false,
        imageUrlRaw: "",
        viewOptions: {
          title: "title",
          subtitle: "type",
          content: "description",
          src: "data"
        }
      };
    },

    mounted() {
      this.init();
    },
    beforeDestroy() {
      this.editor.destroy();
    }
  };
</script>

<style lang="scss">
  @import "assets/scss/editor";
</style>
