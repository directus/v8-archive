<template>
  <div class="no-wrap">{{ value }}</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    name: "readonly-wysiwyg",
    mixins: [mixin]
};
</script>
