<template>
  <div class="interface-checkbox-relational">
    <v-checkbox
      :style="{flexBasis:100/(options.grid||1)+'%'}"
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
  name: "interface-checkbox-relational",
  mixins: [mixin],
  data() {
    return {
      items: [],
      selection: [],
      unchecked: {}
    };
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
    if (!this.newItem) {
      this.selection = this.value.map(
        item => item[this.junctionFieldOfRelated][this.relatedPk]
      );
    }
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

    labelRendered(item) {
      if (this.options.template) {
        return this.$helpers.micromustache.render(this.options.template, item);
      } else {
        return this.item[this.relatedPk];
      }
    },

    //When checkbox is clicked
    onSelection(val, e) {
      let item = this.items.filter(item => item[this.relatedPk] == val)[0];
      if (this.selection.includes(val)) {
        let index = this.selection.indexOf(val);
        this.selection.splice(index, 1);
        this.removeItem(val);
      } else {
        this.selection.push(val);
        this.addItem(val, item);
      }
    },

    prepareItem(item) {
      return { [this.junctionFieldOfRelated]: item };
    },

    /**
     * Adds a new item to junction table
     */
    addItem(val, item) {
      let newValue = [];
      let isSet = false;
      //If the value is set
      //We check throug items if the selected is same.
      if (this.value) {
        this.value.forEach(item => {
          //If the item is deleted
          //Restore the value from unchecked array
          if (item.$delete) {
            let uncheckedItem = this.unchecked[item[this.junctionPk]];
            console.log("UNCHECKED>", uncheckedItem);
            let itemId =
              uncheckedItem[this.junctionFieldOfRelated][this.relatedPk];
            if (itemId == val) {
              isSet = true;
              newValue.push(uncheckedItem);
              delete this.unchecked[item[this.junctionPk]];
            } else {
              newValue.push(item);
            }
          } else {
            newValue.push(item);
          }
        });
      }
      if (!isSet) {
        newValue.push(this.prepareItem(item));
      }
      //newValue.push({ [this.junctionFieldOfRelated]: newItem });
      this.$emit("input", newValue);
    },

    removeItem(val) {
      let newValue = [];
      //Loop through existing value to find an item
      //Set $delete key to true
      this.value.forEach(item => {
        if (
          !item.$delete &&
          item[this.junctionFieldOfRelated][this.relatedPk] == val
        ) {
          //Keep the item in seperate array
          // to restore the value when checked again
          if (item[this.junctionPk]) {
            this.unchecked[item[this.junctionPk]] = item;
            newValue.push({
              [this.junctionPk]: item[this.junctionPk],
              $delete: true
            });
          }
        } else {
          newValue.push(item);
        }
      });

      this.$emit("input", newValue);
    }
  }
};
</script>

<style lang="scss">
.interface-checkbox-relational {
  max-width: var(--width-medium);
  display: flex;
  flex-wrap: wrap;
  margin: -4px;
  .form-checkbox {
    padding: 4px;
  }
}
</style>


