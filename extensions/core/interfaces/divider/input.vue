<template>
  <div :class="`${options.style}-style`">
    <h2 v-if="options.title">{{ options.title }}</h2>
    <hr v-if="options.hr" />
    <p v-if="options.description">{{ options.description }}</p>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin]
};
</script>

<style lang="scss" scoped>
.small-style {
  position: relative;
  margin-top: 60px;
  padding-top: 6px;
  padding-bottom: 20px;
  h2 {
    position: absolute;
    top: 0px;
    left: 50%;
    transform: translateX(-50%);

    color: var(--dark-gray);
    font-size: 0.85em;
    line-height: 1.18;
    font-weight: 700;
    text-transform: uppercase;
    background-color: var(--body-background);
    padding: 0 10px;
  }
  p {
    position: absolute;
    top: 16px;
    left: 50%;
    transform: translateX(-50%);
    color: var(--light-gray);
    font-style: italic;
    max-width: 460px;
  }
  hr {
    border: 0;
    background: none;
    border-bottom: 1px solid var(--lighter-gray);
  }
}

.medium-style {
  margin-top: 60px;
  padding-top: 6px;
  padding-bottom: 20px;
  h2 {
    color: var(--dark-gray);
    font-size: 1.38em;
    line-height: 1.16;
    font-weight: 400;
    margin-bottom: 10px;
  }
  p {
    color: var(--light-gray);
    font-style: italic;
    max-width: 460px;
    margin-top: 6px;
  }
  hr {
    border: 0;
    background: none;
    border-bottom: 1px solid var(--lighter-gray);
  }
}

.large-style {
  margin-top: 60px;
  padding-top: 6px;
  padding-bottom: 20px;
  h2 {
    color: var(--dark-gray);
    font-size: 2em;
    line-height: 1.16;
    font-weight: 300;
    margin-bottom: 10px;
  }
  p {
    color: var(--light-gray);
    font-style: italic;
    max-width: 460px;
    margin-top: 6px;
  }
  hr {
    border: 0;
    background: none;
    border-bottom: 1px solid var(--lighter-gray);
  }
}
</style>
