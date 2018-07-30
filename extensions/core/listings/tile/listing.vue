<template>
  <div class="listing-tile" @scroll="onScroll">
    <v-card
      v-for="item in items"
      :key="item.id"
      :to="item[link]"
      :title="title(item)"
      :subtitle="subtitle(item)"
      :icon="emptySrc(item) ? (viewOptions.icon || 'photo') : null"
      :opacity="emptySrc(item) ? 'half' : null"
      :src="src(item)"
      :body="content(item) "/>
    <v-card
      v-if="lazyLoading"
      color="dark-gray"
      icon="hourglass_empty"
      opacity="half"
      :title="$t('loading_more')" />
  </div>
</template>

<script>
import mixin from "../../../mixins/listing";

export default {
  name: "listing-tile",
  mixins: [mixin],
  methods: {
    title(item) {
      const titleField = this.viewOptions.title || this.primaryKeyField;
      return String(item[titleField]);
    },
    subtitle(item) {
      const subtitleField = this.viewOptions.subtitle || null;

      if (subtitleField) {
        return item[subtitleField] ? String(item[subtitleField]) : "--";
      }

      return null;
    },
    src(item) {
      const srcField = this.viewOptions.src || null;

      if (srcField) {
        if (
          this.fields[srcField] &&
          this.fields[srcField].type.toLowerCase() === "file"
        ) {
          return (
            item[srcField] &&
            item[srcField].storage &&
            item[srcField].storage.full_url
          );
        }

        if (
          srcField === "storage" &&
          this.fields[srcField].collection === "directus_files"
        ) {
          return item[srcField] && item[srcField].full_url;
        }

        return item[srcField] || null;
      }

      return null;
    },
    content(item) {
      const contentField = this.viewOptions.content || null;

      if (contentField) {
        return item[contentField] || null;
      }

      return "--";
    },
    emptySrc(item) {
      return this.viewOptions.src != null && this.src(item) === null;
    },
    onScroll(event) {
      const { scrollHeight, clientHeight, scrollTop } = event.srcElement;
      const totalScroll = scrollHeight - clientHeight;
      const delta = totalScroll - scrollTop;
      if (delta <= 500) this.$emit("next-page");
      this.scrolled = scrollTop > 0;
    }
  }
};
</script>

<style lang="scss" scoped>
.listing-tile {
  padding: 20px;
  display: grid;
  grid-template-columns: repeat(auto-fill, var(--width-small));
  grid-gap: 20px;
  width: 100%;
  height: 100%;
  overflow: scroll;
  -webkit-overflow-scrolling: touch;
  max-height: 100vh;
}
</style>
