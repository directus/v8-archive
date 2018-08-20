<template>
  <div class="no-wrap">{{ cleanValue }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "display-wysiwyg-full",
  mixins: [mixin],
  computed: {
    cleanValue() {
      return this.value
        ? this.value.replace(/<\/?[^>]+(>|$)/g, "").substring(0, 200)
        : "";
    }
  }
};
</script>
