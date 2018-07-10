<template>
  <div class="buttons no-wrap">
    <span
      v-for="(value, index) in displayValue"
      >{{value}}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    mixins: [mixin],
    computed: {
        displayValue() {
            if (Boolean(this.value) === false) {
                return "";
            }

            let value = this.value.split(",");

            if (this.options.wrap) {
                value.pop();
                value.shift();
            }

            if (this.options.format) {
                value = value.map(tag => this.$helpers.formatTitle(tag));
            }

            return value;
        }
    }
};
</script>

<style lang="scss" scoped>
.buttons {
    display: flex;
    flex-wrap: wrap;
    padding: 5px 0;

    span {
        margin: 2px;
        padding: 2px 4px 3px;
        background-color: var(--gray);
        color: var(--white);
        border-radius: var(--border-radius);
    }
}
</style>
