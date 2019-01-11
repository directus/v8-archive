<template>
  <v-table
    ref="table"
    :class="viewOptions.spacing"
    :items="items"
    :columns="columns"
    :primary-key-field="primaryKeyField"
    :selection="selection"
    :sort-val="sortVal"
    :row-height="rowHeight"
    :loading="loading"
    :lazy-loading="lazyLoading"
    :column-widths="viewOptions.widths || {}"
    :link="link"
    :use-interfaces="true"
    :manual-sort-field="sortField"
    @sort="sort"
    @widths="setWidths"
    @select="$emit('select', $event)"
    @scroll-end="$emit('next-page')"
    @input="$emit('input', $event)"
  ></v-table>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  mixins: [mixin],
  computed: {
    columns() {
      const fieldValues = Object.values(this.fields);

      let queryFields;

      if (this.viewQuery.fields) {
        if (Array.isArray(this.viewQuery.fields)) {
          queryFields = this.viewQuery.fields;
        } else {
          queryFields = this.viewQuery.fields.split(",");
        }
      } else {
        queryFields = fieldValues.filter(
          field => field.primary_key === false || field.primary_key === "0"
        )
        .slice(0, 4)
        .map(field => field.field);
      }

      return queryFields
        .filter(field => this.fields[field])
        .map(fieldID => {
          const fieldInfo = this.fields[fieldID];
          const name = fieldInfo.name;
          return { field: fieldID, name, fieldInfo };
        });
    },
    rowHeight() {
      if (this.viewOptions.spacing === "comfortable") {
        return 50;
      }

      if (this.viewOptions.spacing === "cozy") {
        return 40;
      }

      if (this.viewOptions.spacing === "compact") {
        return 30;
      }

      return 40;
    },
    sortVal() {
      let sortQuery =
        (this.viewQuery && this.viewQuery["sort"]) || this.primaryKeyField;

      return {
        asc: !sortQuery.startsWith("-"),
        field: sortQuery.replace("-", "")
      };
    }
  },
  methods: {
    sort(sortVal) {
      const sortValString = (sortVal.asc ? "" : "-") + sortVal.field;

      this.$emit("query", {
        sort: sortValString
      });
    },
    setWidths(widths) {
      this.$emit("options", {
        widths
      });
    }
  },
  watch: {
    sortVal(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.$refs.table.$el.scrollTop = 0;
      }
    }
  }
};
</script>
