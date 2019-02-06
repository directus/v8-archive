<template>
  <form @submit.prevent>
    <label for="spacing" class="style-3 required">{{
      $t("layouts-calendar-date")
    }}</label>
    <v-select
      id="spacing"
      :value="viewOptions.date || '__none__'"
      :options="dateOptions"
      class="select"
      icon="today"
      @input="setOption('date', $event)"
    ></v-select>
    <label for="spacing" class="style-3">{{
      $t("layouts-calendar-time")
    }}</label>
    <v-select
      id="spacing"
      :value="viewOptions.time || '__none__'"
      :options="timeOptions"
      class="select"
      icon="schedule"
      @input="setOption('time', $event)"
    ></v-select>
    <label for="spacing" class="style-3 required">{{
      $t("layouts-calendar-title")
    }}</label>
    <v-select
      id="spacing"
      :value="viewOptions.title || '__none__'"
      :options="textOptions"
      class="select"
      icon="title"
      @input="setOption('title', $event)"
    ></v-select>
    <label for="spacing" class="style-3">{{
      $t("layouts-calendar-color")
    }}</label>
    <v-select
      id="spacing"
      :value="viewOptions.color || '__none__'"
      :options="colorOptions"
      class="select"
      icon="color_lens"
      @input="setOption('color', $event)"
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
      var options = this.$lodash.mapValues(this.fields, info =>
        info.type == "string" || info.type == "integer" ? info.name : null
      );
      return this.$lodash.pickBy(options, _.identity);
    },
    dateOptions() {
      var options = this.$lodash.mapValues(this.fields, info =>
        info.type == "date" ? info.name : null
      );
      return this.$lodash.pickBy(options, _.identity);
    },
    timeOptions() {
      var options = {
        __none__: `(${this.$t("dont_show")})`,
        ...this.$lodash.mapValues(this.fields, info =>
          info.type == "time" ? info.name : null
        )
      };
      return this.$lodash.pickBy(options, _.identity);
    },
    colorOptions() {
      var options = {
        __none__: `(${this.$t("dont_show")})`,
        ...this.$lodash.mapValues(this.fields, info =>
          ["color", "color-palette"].includes(info.interface) ? info.name : null
        )
      };
      return this.$lodash.pickBy(options, _.identity);
    }
  },
  methods: {
    setOption(option, value) {
      this.$emit("options", {
        ...this.viewOptions,
        [option]: value
      });
    }
  }
};
</script>

<style lang="scss" scoped>
label {
  margin-bottom: 10px;
  margin-top: 30px;
}

.required::after {
  content: "required";
  margin: 0 5px;
  padding: 0px 4px 1px;
  font-size: 10px;
  font-weight: 600;
  color: var(--white);
  background-color: var(--warning);
  border-radius: var(--border-radius);
  text-transform: uppercase;
}
</style>
