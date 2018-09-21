<template>
  <div>
    <div v-if="true" class="image"><img :src="url"></div>
    <div v-else class="file">{{file}}</div>
    <div class="toolbar">
      <!-- <span class="download">
        <a :href="url" :download="file"><i class="material-icons">get_app</i></a>
      </span> -->
      <span class="original">
        <a :href="url" target="_blank"><i class="material-icons">link</i>{{url}}</a>
      </span>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  computed: {
    file() {
      return this.values['filename'];
    },
    path() {
      return this.$store.state.auth.url + "/uploads/_/originals/";
    },
    url() {
      return this.$store.state.auth.url + "/uploads/_/originals/" + this.values['filename'];
    }
  }
};
</script>

<style lang="scss" scoped>
.image {
  width: 100%;
  background-color: var(--black);
  text-align: center;
  border-radius: var(--border-radius);
  img {
    margin: 0 auto;
    max-height: 400px;
    max-width: 100%;
    display: block;
  }
}
.toolbar {
  a {
    transition: var(--fast) var(--transition);
    text-decoration: none;
    color: var(--gray);
    &:hover {
      color: var(--darker-gray);
    }
  }
  span {
    margin-right: 10px;
    vertical-align: middle;
  }
  .original {
    display: inline-block;
    margin-top: 10px;
    i {
      margin-right: 6px;
    }
  }
}
</style>
