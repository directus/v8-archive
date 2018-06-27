<template>
  <form @submit.prevent>
    <label for="spacing">Spacing</label>
    <v-select
      id="spacing"
      :value="viewOptions.spacing || 'cozy'"
      :options="{
        compact: 'Compact',
        cozy: 'Cozy',
        comfortable: 'Comfortable',
      }"
      class="select"
      icon="reorder"
      @input="setSpacing" />
    <fieldset>
      <legend>{{ $t('listings-tabular-fields') }}</legend>
      <draggable v-model="sortList" @end="sort">
        <div class="draggable" v-for="(field) in sortList">
          <v-checkbox
            class="checkbox"
            :key="field.field"
            :id="field.field"
            :label="field.name"
            :value="field.field"
            :checked="fieldsInUse.includes(field.field)"
            @change="toggleField(field.field)" />
          <i class="material-icons">drag_handle</i>
        </div>
      </draggable>
    </fieldset>
  </form>
</template>

<script>
import mixin from "../../../mixins/listing";

export default {
  mixins: [mixin],
  data() {
    return {
      sortList: null
    };
  },
  computed: {
    fieldsInUse() {
      if (!this.viewQuery || !this.viewQuery.fields)
        return Object.keys(this.fields);
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
    setSpacing(value) {
      this.$emit("options", {
        spacing: value
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
