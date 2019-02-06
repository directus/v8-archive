<template>
  <div>
    <v-checkbox
      v-for="item in items"
      :id="uid(item)"
      :key="`checkbox_relational_${item.id}`"
      :value="item[itemPk]"
      :disabled="readonly"
      :label="labelRendered(item)"
      :checked="selection.includes(item[itemPk])"
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
  created() {
    //Find the field name for the junction table entry
    let field = this.relation.junction.field_many.field;
    this.selection = this.value.map(item => item[field][this.itemPk]);
    this.getItems();
  },
  computed: {
    //The primary key of related table
    itemPk() {
      let fields = this.relation.junction.collection_one.fields;
      return this.$lodash.find(fields, {
        primary_key: true
      }).field;
    }
  },
  methods: {
    uid(item) {
      return this.$helpers.shortid.generate() + "_" + item.id;
    },

    labelRendered(val) {
      if (this.options.template) {
        //Find the field name for the junction table entry
        let field = this.relation.junction.field_many.field;
        return this.$helpers.micromustache.render(this.options.template, {
          [field]: val
        });
      } else {
        return this.item[this.itemPk];
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
      //Find the field name for the junction table entry
      let field = this.relation.junction.field_many.field;
      let selected = this.items
        .filter(item => this.selection.includes(item.id))
        .map(item => {
          return {
            [field]: item
          };
        });
      console.log(selected);
      this.$emit("input", selected);
    },

    // Get items to render the checkboxes
    getItems() {
      //Get the collection name of the related table
      let collection = this.relation.junction.collection_one.collection;
      this.$api.getItems(collection, {}).then(res => {
        this.items = res.data;
        console.log("ITEMS", res.data);
      });
    }
  }
};
</script>

