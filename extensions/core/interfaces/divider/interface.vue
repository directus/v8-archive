<template>
  <div :class="`line-style-${options.style}`">
    <hr v-if="!options.input"/>
    <span v-if="options.input">{{options.input}}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
export default {
  mixins: [mixin],
  computed: {
    hello() {
      console.log("yo");
    }
  }
};
</script>

<style lang="scss" scoped>
.line-style {
  position: relative;
}
.line-style-normal > hr {
  border-top: 1px solid var(--lighter-gray);
}

.line-style-text {
  display: block;
  text-align: center;
  overflow: hidden;
  white-space: nowrap;
}

.line-style-text > span {
  position: relative;
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
</style>


