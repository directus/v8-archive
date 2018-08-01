<template>
  <div v-if="options.style === 'text-small'" :class="`line-style-${options.style}`">
      <span>{{options.title}}</span>
      <p><em>{{options.description}}</em></p>
  </div>
  <div v-else-if="options.style === 'basic'" :class="`line-style-${options.style}`">
      <hr />
  </div>
  <div v-else :class="`line-style-${options.style}`">
    <hr />
    <h3>{{options.title}}</h3>
    <p><em>{{options.description}}</em></p>
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
.line-style-text-small {
  display: block;
  text-align: center;
  overflow: hidden;
  white-space: nowrap;

  hr {
    display: none;
  }
}

.line-style-text-small > span {
  position: relative;
  text-transform: uppercase;
  font-size: 11px;
  font-weight: 700;
  color: var(--gray);
  display: inline-block;
}

.line-style-text-small > p {
  font-size: 11px;
  color: var(--light-gray);
}

.line-style-text-small > span:before,
.line-style-text-small > span:after {
  content: "";
  position: absolute;
  top: 50%;
  width: 9999px;
  height: 1px;
  background: var(--lighter-gray);
}

.line-style-text-small > span:before {
  right: 100%;
  margin-right: 15px;
}

.line-style-text-small > span:after {
  left: 100%;
  margin-left: 15px;
}

.line-style-text-small > p {
  text-decoration-style: dashed;
}

// small

.line-style-small {
  width: 100%;
  max-width: var(--width-small);
  margin: 0 auto;

  h3 {
    text-decoration: uppercase;
  }
}

.line-style-normal {
  width: 100%;
  max-width: var(--width-normal);
  margin: 0 auto;

  h3 {
    text-decoration: uppercase;
  }
}

.line-style-big {
  width: 100%;
  max-width: var(--width-large);
  margin: 0 auto;

  h3 {
    text-decoration: uppercase;
  }
}

// options

.line-style-standard {
  width: 100%;

  hr {
    border-top: 2px solid var(--lighter-gray);
  }

  h3 {
    font-size: 26px;
    font-weight: lighter;
    color: var(--dark-gray);
  }

  p {
    font-weight: 100;
    width: 100%;
    max-width: 20em;
  }
}

.line-style-basic > hr {
  border-top: 1px solid var(--lighter-gray);
}
</style>
