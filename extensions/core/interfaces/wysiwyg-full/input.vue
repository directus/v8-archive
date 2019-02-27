<template>
    <div class="interface-wysiwyg-container editor"
         :id="name"
         :name="name"
         @input="$emit('input', $event.target.innerHTML)"
    >
        <editor-menu-bar :editor="editor">
            <div class="menubar" slot-scope="{ commands, isActive }">
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
                        :class="{ 'is-active': isActive.code_block() }"
                        @click="commands.code_block"
                >
                    <icon name="code"/>
                </button>
                <button
                        class="menubar__button"
                        @click="showImagePrompt(commands.image)"
                >
                    <icon name="image" />
                </button>
                <button
                        class="menubar__button"
                        @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })"
                >
                    <icon name="table_chart" />
                </button>

                <span v-if="isActive.table()">
						<button
                                class="menubar__button"
                                @click="commands.deleteTable"
                        >
							<icon name="delete_table" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addColumnBefore"
                        >
							<icon name="add_col_before" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addColumnAfter"
                        >
							<icon name="add_col_after" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.deleteColumn"
                        >
							<icon name="delete_col" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addRowBefore"
                        >
							<icon name="add_row_before" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.addRowAfter"
                        >
							<icon name="add_row_after" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.deleteRow"
                        >
							<icon name="delete_row" />
						</button>
						<button
                                class="menubar__button"
                                @click="commands.toggleCellMerge"
                        >
							<icon name="combine_cells" />
						</button>
					</span>

                <button
                        class="menubar__button"
                        @click="commands.horizontal_rule"
                >
                    <icon name="maximize"/>
                </button>
            </div>
        </editor-menu-bar>
            <editor-content ref="editor" :class="['interface-wysiwyg', (readonly ? 'readonly' : '')]" class="editor__content" :editor="editor"/>
    </div>
</template>

<script>
    import Icon from './components/icon'
    import {Editor, EditorContent, EditorMenuBar} from 'tiptap'
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
    } from 'tiptap-extensions'

    import mixin from "../../../mixins/interface";

    export default {
        name: "interface-wysiwyg",
        mixins: [mixin],
        watch: {
            value(newVal) {
                if (newVal) {
                   console.log(this.editor.view.dom.innerHTML)
                    console.log(this)
                    console.log(this.value)
                    //return this.value.target.innerHTML
                }
            },
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
                        new TableRow(),
                    ],
                    content: "",
                });

                if (this.value) {
                    this.editor.setContent(this.value);
                }
            },

            showImagePrompt(command) {
                const src = prompt('Enter the url of your image here')
                if (src !== null) {
                    command({ src })
                }
            },

            destroy() {
                this.editor.destroy();
            },

        },
        components: {
            EditorContent,
            EditorMenuBar,
            Icon
        },

        data() {
            return {
                editor: null,
            }
        },

        mounted() {
            this.init();
        },
        beforeDestroy() {
            this.editor.destroy()
        },
    }
</script>

<style lang="scss">
    .editor {
        .menubar__button {
            position: relative;
            span, i {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
             .label {
                top: calc(50% - 4px);
                left: calc(50% + 2px);
                transform: translate(-50%, -50%);
                font-size: 8px;
                letter-spacing: -1px;
            }
        }
        .tableWrapper {
            max-width: 100%;
            overflow-x: auto;
            table {
                background-color: solid var(--lightest-gray) 1px;
                border: solid var(--gray) 1px;
                width: 100%;
                tbody {
                    tr, td {
                        min-width: 70px;
                        border: 1px solid var(--gray);
                    }
                }
            }
        }
    }

</style>
