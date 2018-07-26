<template>
    <div :class="`line-style-${options.style} ${options.weight}`">
        <hr />
        <span>{{options.input}}</span>
    </div>
</template>

<script>
import mixin from "../../../mixins/interface";
export default {
  mixins: [mixin],
  computed: {}
};
</script>

 <style lang="scss" scoped>
</style>
