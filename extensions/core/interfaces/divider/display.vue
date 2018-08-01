<template>
<div v-if="options.style === 'text-small'" :class="`line-style-${options.style}`">
      <span>{{options.title}}</span>
      <p><em>{{options.description}}</em></p>
  </div>
  <div v-else :class="`line-style-${options.style}`">
    <hr />
    <h3>{{options.title}}</h3>
    <p><em>{{options.description}}</em></p>
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
