<template>
  <v-input
    type="datetime-local"
    class="interface-datetime"
    :id="name"
    :name="name"
    :min="options.min"
    :max="options.max"
    :readonly="readonly"
    :value="ISO"
    @input="updateValue" />
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    mixins: [mixin],
    computed: {
        date() {
            if (!this.value) return;
            return this.$helpers.date.sqlToDate(this.value);
        },
        ISO() {
            if (!this.value) return;
            const ISOString = this.date.toISOString();
            return ISOString.substring(0, ISOString.length - 1);
        }
    },
    methods: {
        updateValue(value) {
            this.$emit("input", this.$helpers.date.dateToSql(new Date(value)));
        }
    }
};
</script>

<style lang="scss" scoped>
.interface-datetime {
    max-width: var(--width-small);
}
</style>
