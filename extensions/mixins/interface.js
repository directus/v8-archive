module.exports = {
  props: {
    name: {
      type: String,
      required: true
    },
    value: {
      type: null,
      default: null
    },
    type: {
      type: String,
      required: true
    },
    length: {
      type: [String, Number],
      default: null
    },
    readonly: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },
    options: {
      type: Object,
      default: () => ({})
    },
    newItem: {
      type: Boolean,
      default: false
    },
    relationship: {
      type: Object,
      default: null
    },
    fields: {
      type: Object,
      required: true
    },
    values: {
      type: Object,
      required: true
    }
  }
};
