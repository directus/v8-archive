<template>
  <div :class="`line-style-${options.style} ${options.weight}`">
    <hr />
    <span v-if="options.input">{{options.input}}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
export default {
  mixins: [mixin],
  computed: {}
};
</script>

<style lang="scss" scoped>
// text
.line-style-text {
  display: block;
  text-align: center;
  overflow: hidden;
  white-space: nowrap;

  hr {
    display: none;
  }
}

.line-style-text > span {
  position: relative;
  text-transform: uppercase;
  font-size: 0.85em;
  font-weight: 700;
  display: inline-block;
}

.line-style-text > span:before,
.line-style-text > span:after {
  content: "";
  position: absolute;
  top: 50%;
  width: 9999px;
  height: 1px;
  background: var(--lighter-gray);
}

.line-style-text > span:before {
  right: 100%;
  margin-right: 15px;
}

.line-style-text > span:after {
  left: 100%;
  margin-left: 15px;
}

// small

.line-style-small {
  width: 100%;
  max-width: var(--width-small);
  margin: 0 auto;
  span {
    display: none;
  }
}

.line-style-normal {
  width: 100%;
  max-width: var(--width-normal);
  margin: 0 auto;

  span {
    display: none;
  }
}

.line-style-big {
  width: 100%;
  max-width: var(--width-large);
  margin: 0 auto;
  span {
    display: none;
  }
}

// options

// weights
.hairline > hr {
  border-top: 1px solid var(--lighter-gray);
}

.thin > hr {
  border-top: 2.5px solid var(--lighter-gray);
}

.medium > hr {
  border-top: 3.2px solid var(--lighter-gray);
}

.heavy {
  hr {
    border-top: 5px solid var(--lighter-gray);
  }
}
.line-style-standard {
  span {
    display: none;
  }
}
</style>
