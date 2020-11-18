module.exports = {
  props: {
    // Unique id for this interface. Should be added to the lower level 
    // HTML input element if applicable. This ID will be used in the label's
    // `for` attribute
    id: {
      type: String,
      required: true
    },
    // Name of the field
    name: {
      type: String,
      required: true
    },
    // The current value. This can either be the default value, the saved database
    // value or the current state after the user made an edit
    value: {
      type: null,
      default: null
    },
    // Type of the field, eg `string`, `hash`, or `array`
    type: {
      type: String,
      required: true
    },
    // Max length
    length: {
      type: [String, Number],
      default: null
    },
    // If the field is readonly or not
    readonly: {
      type: Boolean,
      default: false
    },
    // Name of the collection
    collection: {
      type: String,
      default: null
    },
    // Primary key of the item you're editing in this context
    primaryKey: {
      type: [Number, String],
      default: null
    },
    // If the field is required or not
    required: {
      type: Boolean,
      default: false
    },
    // Field options. A json object based on the interface's meta.json file
    options: {
      type: Object,
      default: () => ({})
    },
    // If the item that's currently being edited is new
    newItem: {
      type: Boolean,
      default: false
    },
    // The relation of the current field. Will contain information on the related
    // collection and field(s)
    relation: {
      type: Object,
      default: null
    },
    // The other fields in the current edit page
    fields: {
      type: Object,
      default: null
    },
    // The values of the other fields on the edit page. Can be used for things like
    // automatically generating a slug based on another field
    values: {
      type: Object,
      default: null
    },
    width: {
      type: String,
      default: null,
      validator(val) {
        return [
          'half',
          'half-left',
          'half-right',
          'full',
          'fill'
        ].includes(val);
      }
    }
  }
};
