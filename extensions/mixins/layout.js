module.exports = {
  props: {
    primaryKeyField: {
      type: String,
      required: true
    },
    fields: {
      type: Object,
      required: true
    },
    items: {
      type: Array,
      default: () => ([])
    },
    viewOptions: {
      type: Object,
      default: () => ({})
    },
    viewQuery: {
      type: Object,
      default: () => ({})
    },
    loading: {
      type: Boolean,
      default: false
    },
    lazyLoading: {
      type: Boolean,
      default: false
    },
    selection: {
      type: Array,
      default: () => []
    },
    link: {
      type: String,
      default: null
    },
    sortField: {
      type: String,
      default: null
    }
  }
};
