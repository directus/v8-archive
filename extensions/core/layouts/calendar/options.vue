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
    },
    fieldsInUse() {
      if (!this.viewQuery || !this.viewQuery.fields)
        return Object.values(this.fields)
          .filter(field => field.primary_key === false)
          .slice(0, 5)
          .map(field => field.field);

      if (this.viewQuery.fields === "") return [];

      return this.viewQuery.fields
        .split(",")
        .filter(field => this.fields[field]);
    }
  },
  created() {
    this.initSortList();
  },
  methods: {
    setOption(option, value) {
      this.$emit("options", {
        ...this.viewOptions,
        [option]: value
      });
    },
    toggleField(fieldID) {
      const fieldsInUse = [...this.fieldsInUse];

      if (fieldsInUse.includes(fieldID)) {
        fieldsInUse.splice(fieldsInUse.indexOf(fieldID), 1);
      } else {
        fieldsInUse.push(fieldID);
      }

      const fields = this.sortList
        .map(fieldInfo => fieldInfo.field)
        .filter(fieldID => fieldsInUse.includes(fieldID))
        .join();

      this.$emit("query", {
        fields
      });
    },
    sort() {
      this.$emit("query", {
        ...this.viewQuery,
        fields: this.sortList
          .map(obj => obj.field)
          .filter(fieldID => this.fieldsInUse.includes(fieldID))
          .join()
      });
    },
    initSortList() {
      this.sortList = [
        ...this.fieldsInUse.map(fieldID => this.fields[fieldID]),
        ...Object.values(this.fields).filter(
          fieldInfo => !this.fieldsInUse.includes(fieldInfo.field)
        )
      ];
    }
  },
  watch: {
    fields() {
      this.initSortList();
    }
  }
};
</script>

<style lang="scss" scoped>
fieldset {
  padding: 8px 0 0 0;
}

label {
  margin-bottom: 10px;
  margin-top: 30px;
}

.draggable {
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: ns-resize;

  .checkbox {
    max-width: 125px;
  }

  i {
    color: var(--lighter-gray);
  }
}
</style>
