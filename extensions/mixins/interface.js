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
    relation: {
      type: Object,
      default: null
    },
    fields: {
      type: Object,
      default: null
    },
    values: {
      type: Object,
      default: null
    }
  }
};
