<template>
  <div v-if="checkbox" class="checkbox">
    <input
      type="checkbox"
      :disabled="readonly"
      :id="name"
      @change="updateValue($event.target.checked);"
    />
    <label :for="name">
      <i class="material-icons icon">{{ icon }}</i> {{ label }}
    </label>
  </div>

  <div v-else class="toggle">
    <input
      type="checkbox"
      :disabled="readonly"
      :id="name"
      :checked="value"
      @change="updateValue($event.target.checked);"
    />
    <label :for="name"><span></span>{{ label }}&nbsp;</label>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    icon() {
      return this.value ? "check_box" : "check_box_outline_blank";
    },
    checkbox() {
      return this.options.checkbox || false;
    },
    label() {
      return this.value ? this.options.labelOn : this.options.labelOff;
    }
  },
  methods: {
    updateValue(value) {
      this.$emit("input", value);
    }
  }
};
</script>

<style lang="scss" scoped>
input {
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
  opacity: 0;
  z-index: 2;
  cursor: pointer;
}
.checkbox {
  position: relative;
  display: inline-block;
  .material-icons {
    margin-top: -2px;
  }
  label {
    transition: all var(--fast) var(--transition);
    color: var(--gray);
    padding: 0.5rem 0 0.5rem 0;
  }
  &:hover label {
    color: var(--darker-gray);
  }
  input:checked + label {
    color: var(--accent);
  }
  input:disabled + label {
    color: var(--light-gray);
  }
  input:disabled {
    cursor: not-allowed;
  }
}
.toggle {
  position: relative;
  display: inline-block;

  &:hover {
    label:after {
      background-color: var(--lighter-gray);
    }
  }

  label {
    padding: 0.5rem 0 0.5rem 2.75rem;
    position: relative;

    span {
      position: relative;
      vertical-align: middle;
    }

    &:before,
    &:after {
      content: "";
      position: absolute;
      margin: 0;
      outline: 0;
      top: 50%;
      transform: translate(0, -50%);
      transition: all 300ms var(--transition);
      cursor: pointer;
    }

    &:before {
      left: 0.0625rem;
      width: 2.125rem;
      height: 0.875rem;
      border-radius: 0.5rem;
      background-color: var(--gray);
    }

    &:after {
      left: 0;
      width: 1.25rem;
      height: 1.25rem;
      background-color: var(--lightest-gray);
      border-radius: 50%;
      box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.14),
        0 2px 2px 0 rgba(0, 0, 0, 0.098), 0 1px 5px 0 rgba(0, 0, 0, 0.084);
    }
  }

  input:checked + label {
    color: var(--accent);
    &:before {
      background-color: var(--accent);
      opacity: 0.4;
    }
    &:after {
      background-color: var(--accent);
      transform: translate(80%, -50%);
    }
  }

  input:disabled + label {
    color: var(--light-gray);
    &:before,
    &:after {
      background-color: var(--light-gray);
    }
    &:before {
      opacity: 0.4;
    }
  }

  input:disabled {
    cursor: not-allowed;
  }
}
</style>
