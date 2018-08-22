<template>
  <div class="layout-cards" @scroll="onScroll">
    <v-card
      v-for="item in items"
      :key="item.id"
      :to="item[link]"
      :title="title(item)"
      :subtitle="subtitle(item)"
      :icon="emptySrc(item) ? (viewOptions.icon || 'photo') : null"
      :opacity="emptySrc(item) ? 'half' : null"
      :src="src(item)"
      :body="content(item)"
      :selected="selection.includes(item.id)"
      :selection-mode="selection.length > 0"
      @select="select(item.id)"></v-card>
    <v-card
      v-if="lazyLoading"
      color="dark-gray"
      icon="hourglass_empty"
      opacity="half"
      :title="$t('loading_more')"></v-card>
  </div>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  name: "layout-cards",
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
            item[srcField].data &&
            item[srcField].data.thumbnails &&
            item[srcField].data.thumbnails[0] &&
            item[srcField].data.thumbnails[0].url
          );
        }

        if (
          srcField === "data" &&
          this.fields[srcField].collection === "directus_files"
        ) {
          return item[srcField] &&
            item[srcField].thumbnails &&
            item[srcField].thumbnails[0] &&
            item[srcField].thumbnails[0].url
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
    },
    select(id) {
      let newSelection;

      if (this.selection.includes(id)) {
        newSelection = this.selection.filter(selectedID => selectedID !== id);
      } else {
        newSelection = [...this.selection, id];
      }

      this.$emit("select", newSelection);
    }
  }
};
</script>

<style lang="scss" scoped>
.layout-cards {
  padding: var(--page-padding);
  padding-bottom: var(--page-padding-bottom);
  display: grid;
  grid-template-columns: repeat(auto-fill, var(--width-small));
  grid-gap: 20px;
  width: 100%;
}
</style>
