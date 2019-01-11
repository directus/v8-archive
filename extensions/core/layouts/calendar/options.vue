<template>
  <form @submit.prevent>
    <label for="spacing" class="style-3">Date</label>
    <v-select
      id="spacing"
      :value="viewOptions.date || '__none__'"
      :options="dateOptions"
      class="select"
      icon="reorder"
      @input="setOption('date', $event)"
    ></v-select>
    <label for="spacing" class="style-3">Time</label>
    <v-select
      id="spacing"
      :value="viewOptions.time || '__none__'"
      :options="timeOptions"
      class="select"
      icon="reorder"
      @input="setOption('time', $event)"
    ></v-select>
    <label for="spacing" class="style-3">Title</label>
    <v-select
      id="spacing"
      :value="viewOptions.title || '__none__'"
      :options="textOptions"
      class="select"
      icon="reorder"
      @input="setOption('title', $event)"
    ></v-select>
  </form>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  mixins: [mixin],
  data() {
    return {
      sortList: null
    };
  },
  computed: {
    textOptions() {
      var options = this.$lodash.mapValues(this.fields, info => (info.type == "string" || info.type == "integer")? info.name : null);
      return this.$lodash.pickBy(options, _.identity);
    },
    dateOptions() {
      var options = this.$lodash.mapValues(this.fields, info => info.type == "date"? info.name : null);
      return this.$lodash.pickBy(options, _.identity);
    },
    timeOptions() {
      var options = {__none__: `(${this.$t("dont_show")})`,
        ...this.$lodash.mapValues(this.fields, info => info.type == "time"? info.name : null)};
      return this.$lodash.pickBy(options, _.identity);
    }
  },
  methods: {
    setOption(option, value) {
      this.$emit("options", {
        ...this.viewOptions,
        [option]: value
      });
    },
  }
};
</script>

<style lang="scss" scoped>

label {
  margin-bottom: 10px;
  margin-top: 30px;
}
</style>
