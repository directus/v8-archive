<template>
  <div class="translation">
    <v-form
      :fields="relatedFields"
      :values="{}"
      :collection="relation.collection_many"
      ref="form"
      @unstage-value="unstageValue"
      @stage-value="stageValue" />
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    relatedFields() {
      if (!this.relation) return null;
      return this.$store.state.collections[this.relation.collection_many]
        .fields;
    }
  }
};
</script>

<style lang="scss" scoped>
.translation {
  width: 100%;
  max-width: var(--width-large);
  padding: var(--page-padding);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
}
</style>
