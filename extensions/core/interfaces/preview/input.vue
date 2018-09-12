<template>
  <v-button
    class="launch"
    :disabled="required && disabled"
    @click="preview">
      {{ $t('interfaces-preview-preview_item') }} <i class="material-icons">launch</i>
  </v-button>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  methods: {
      preview: function (event) {

        let url = this.$helpers.micromustache.render(
          this.options.url_template, // "Hello {{text}}!"
          this.values // { text: "World" }
        );

        window.open(url, '_blank');
      }
    }
};
</script>

<style lang="scss" scoped>
.input {
  max-width: var(--width-small);
}
.launch {
  margin-top: 10px;
  .material-icons {
    font-size: 18px;
    margin-left: 10px;
    vertical-align: middle;
  }
}
</style>
