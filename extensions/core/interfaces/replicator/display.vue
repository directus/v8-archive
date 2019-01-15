<template>
  <ul class="replicator-display">
    <li v-for="(value, key) in itemCount" :key="key">
      <b>{{ key }}</b> with {{ value }} elements
    </li>
  </ul>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "ReplicatorDisplay",
  mixins: [mixin],
  computed: {
    /**
     * @returns {Object}
     */
    parsedValue() {
      return JSON.parse(this.value) || {};
    },
    itemCount() {
      return Object.entries(this.parsedValue).reduce((prev, [key, value]) => {
        prev[key] = value.length;
        return prev;
      }, {});
    }
  }
};
</script>

<style lang="scss" scoped>
.replicator-display {
  list-style: none;
  margin: 0;
  padding: 0;
  font-weight: normal;
  b {
    font-weight: bold;
  }
  li {
    display: inline-block;
    font-size: 0.8rem;
    white-space: nowrap;
    background: var(--accent);
    padding: 0.25rem;
    margin-right: 0.25rem;
    color: var(--white);
    border-radius: 3px;

    // &:not(:last-child) {
    //   &::after {
    //     content: "|";
    //     display: inline-block;
    //     margin: 0 0.5rem;
    //   }
    // }
  }
}
</style>
