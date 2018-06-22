<template>
  <textarea
    ref="textarea"
    :readonly="readonly"
    :placeholder="options.placeholder"
    :value="processedValue"
    rows="10"
    @keydown="process"
    @input="updateValue($event.target.value)"/>
</template>

<script>
import mixin from '../../../mixins/interface';

export default {
  mixins: [mixin],
  data() {
    return {
      valid: null,
      lastValue: this.value,
      processedValue: this.value,
    };
  },
  watch: {
    value() {
      try {
        this.processedValue = JSON.stringify(JSON.parse(this.value), null, this.options.indent);
      } catch(e) {
        return '';
      }
    },
  },
  methods: {
    updateValue(value) {
      if (this.options.valid) {
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
      return (match && match[0].length / indent.length) || 0;
    },
    addIndent(before, after, num) {
      if (!num) return;

      const textarea = this.$refs.textarea;
      const indent = this.options.indent;
      const newValue = before + '\n' + indent.repeat(num) + after;

      this.processedValue = newValue;
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

      this.processedValue = newValue;
      this.lastValue = newValue;
      textarea.selectionStart = selection;
      textarea.selectionEnd = selection;
    }
  },
}
</script>

<style lang="scss" scoped>
textarea {
  width: 100%;
  font-family: monospace;
  max-width: var(--width-normal);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  color: var(--gray);
  padding: 10px;
  line-height: 1.5;
  transition: var(--fast) var(--transition);
  transition-property: color, border-color;

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

  input:-webkit-autofill,
  input:-webkit-autofill:hover,
  input:-webkit-autofill:focus
  input:-webkit-autofill,
  textarea:-webkit-autofill,
  textarea:-webkit-autofill:hover
  textarea:-webkit-autofill:focus {
    border: var(--input-border-width) solid var(--lighter-gray);
    background-color: var(--white);
    box-shadow: inset 0 0 0 2000px var(--white);
  }
}
</style>
