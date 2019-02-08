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
      selection: []
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
    this.selection = this.value.map(
      item => item[this.junctionFieldOfRelated][this.relatedPk]
    );
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

    /**
     * Adds a new item to junction table
     */
    addItem(val, item) {
      // let newValue = [];
      // this.value.forEach(item => {
      //   if (item[this.junctionFieldOfRelated][this.relatedPk] == val) {
      //     delete item.$delete;
      //     newValue.push(item);
      //   } else {
      //     newValue.push(item);
      //   }
      // });

      let newValue = [
        ...this.value,
        {
          [this.junctionFieldOfRelated]: item
        }
      ];
      this.$emit("input", newValue);
    },

    removeItem(val) {
      let newValue = [];
      //Loop through existing value to find an item
      //Set $delete key to true
      this.value.forEach(item => {
        if (item[this.junctionFieldOfRelated][this.relatedPk] == val) {
          newValue.push({
            //...item,
            [this.junctionPk]: item[this.junctionPk],
            $delete: true
          });
        } else {
          newValue.push(item);
        }
      });
      this.$emit("input", newValue);
    }
  }
};
</script>

