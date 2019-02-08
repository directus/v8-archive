<template>
  <div>
    <v-checkbox
      v-for="item in items"
      :id="uid(item)"
      :key="`checkbox_relational_${item.id}`"
      :value="item[relatedPk]"
      :disabled="readonly"
      :label="labelRendered(item)"
      :checked="selection.includes(item[relatedPk])"
      @change="onSelection(item.id,$event)"
    ></v-checkbox>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-checkboxes-relational",
  mixins: [mixin],
  data() {
    return {
      items: [],
      initiallySelected: [],
      selection: [],
      pkMap: {}
    };
  },
  watch: {
    value(newValue) {
      this.setValues();
    }
  },
  computed: {
    //The primary key of related table
    relatedPk() {
      let fields = this.relation.junction.collection_one.fields;
      return this.$lodash.find(fields, {
        primary_key: true
      }).field;
    },

    //The column name of junction table in ref to related table
    junctionFieldOfRelated() {
      return this.relation.junction.field_many.field;
    },

    //Junction Table Primary Key
    //? In normal case it would be "ID" only!
    junctionPk() {
      return this.$lodash.find(this.relation.collection_many.fields, {
        primary_key: true
      }).field;
    }
  },
  created() {
    //Get all the items
    this.getItems();

    //Select the items
    this.selection = this.value.map(
      item => item[this.junctionFieldOfRelated][this.relatedPk]
    );

    //Copy the initial values to seperate array
    //To make final list when updating
    this.initiallySelected = this.selection.slice(0);

    this.setValues();
  },

  methods: {
    uid(item) {
      return this.$helpers.shortid.generate() + "_" + item.id;
    },

    // Get items to render the checkboxes
    getItems() {
      //Get the collection name of the related table
      let collection = this.relation.junction.collection_one.collection;
      this.$api.getItems(collection, {}).then(res => {
        this.items = res.data;
      });
    },

    setValues() {
      //JunctionKeyVsRelatedKey
      this.value.forEach(item => {
        if (!item.$delete) {
          let relatedPk = item[this.junctionFieldOfRelated][this.relatedPk];
          let junctionPk = item[this.junctionPk];
          if (junctionPk) {
            this.pkMap[relatedPk] = junctionPk;
          }
        }
      });
    },

    labelRendered(val) {
      if (this.options.template) {
        return this.$helpers.micromustache.render(this.options.template, {
          [this.junctionFieldOfRelated]: val
        });
      } else {
        return this.item[this.relatedPk];
      }
    },

    //When checkbox is clicked
    onSelection(val, e) {
      if (this.selection.includes(val)) {
        let index = this.selection.indexOf(val);
        this.selection.splice(index, 1);
      } else {
        this.selection.push(val);
      }
      this.emitValue(val);
    },

    emitValue(val) {
      let item = this.items.filter(item => item[this.relatedPk] == val)[0];
      let newValue;

      //If the item is checked
      let isSelected = this.selection.includes(item[this.relatedPk]);
      //If the item is not intitially selected
      let isInitiallySelected = this.initiallySelected.includes(
        item[this.relatedPk]
      );

      //Add New Item
      if (isSelected && !isInitiallySelected) {
        newValue = {
          [this.junctionFieldOfRelated]: item
        };
      }
      //Delete Item
      else if (!isSelected && isInitiallySelected) {
        newValue = {
          [this.junctionPk]: this.pkMap[item[this.junctionPk]],
          $delete: true
        };
      }
      // //Keep item as it is
      // else if (isSelected && isInitiallySelected) {
      //   newValue = {
      //     [this.junctionPk]: this.pkMap[item[this.junctionPk]],
      //     [this.junctionFieldOfRelated]: item
      //   };
      // }

      let toEmit = this.value.filter(item => {
        if (item[this.relatedPk] == val) {
          return newValue;
        } else {
          return item;
        }
      });
      console.log(toEmit);
      this.$emit("input", toEmit);
    }
  }
};
</script>

