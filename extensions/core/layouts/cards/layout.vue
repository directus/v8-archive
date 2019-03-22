<template>
  <div class="layout-cards" @scroll="onScroll">
    <div class="toolbar">
      <p>{{ $t("sort_by") }}</p>

      <div class="sort-select">
        <select @input="setSort($event.target.value)" :value="sortedOn">
          <option
            v-for="(fieldInfo, name) in fields"
            :key="name"
            :value="name"
            >{{ $helpers.formatTitle(name) }}</option
          >
        </select>
        <i class="material-icons">arrow_drop_down</i>
      </div>

      <div class="sort-select">
        <select
          @input="setSortDirection($event.target.value)"
          :value="sortDirection"
        >
          <option value="asc">ASC</option>
          <option value="desc">DESC</option>
        </select>
        <i class="material-icons">arrow_drop_down</i>
      </div>
    </div>

    <div class="cards" :class="{ loading: loading }">
      <v-card
        v-for="item in items"
        :key="item.id"
        :to="item[link]"
        :title="title(item)"
        :subtitle="subtitle(item)"
        :icon="emptySrc(item) ? viewOptions.icon || 'photo' : null"
        :opacity="emptySrc(item) ? 'half' : null"
        :src="src(item)"
        :body="content(item)"
        :selected="selection.includes(item.id)"
        :selection-mode="selection.length > 0"
        @select="select(item.id)"
      ></v-card>
      <v-card
        v-if="lazyLoading"
        color="dark-gray"
        icon="hourglass_empty"
        opacity="half"
        :title="$t('loading_more')"
      ></v-card>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/layout";

export default {
  name: "layout-cards",
  mixins: [mixin],
  computed: {
    sortedOn() {
      let fieldName;

      if (this.viewQuery.sort) {
        fieldName = this.viewQuery.sort;
      } else {
        // If the user didn't sort, default to the first field
        fieldName = Object.keys(this.fields)[0];
      }

      // If the sort viewQuery was already descending, remove the - so we don't
      // run into server errors with double direction characters
      if (fieldName.startsWith("-")) fieldName = fieldName.substring(1);

      return fieldName;
    },
    sortDirection() {
      if (!this.viewQuery.sort) return "asc";

      if (this.viewQuery.sort.substring(0, 1) === "-") return "desc";

      return "asc";
    }
  },
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
          return (
            item[srcField] &&
            item[srcField].thumbnails &&
            item[srcField].thumbnails[0] &&
            item[srcField].thumbnails[0].url
          );
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

      return null;
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
    },
    setSort(fieldName) {
      this.$emit("query", {
        sort: fieldName
      });
    },
    setSortDirection(direction) {
      this.$emit("query", {
        sort: (direction === "desc" ? "-" : "") + this.sortedOn
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.layout-cards {
  overflow: auto;
  height: 100%;
  max-height: calc(100vh - var(--header-height));
}

.toolbar {
  background-color: var(--white);
  width: 100%;
  position: sticky;
  top: 0;
  z-index: +1;
  height: var(--header-height);
  padding: var(--page-padding);
  display: flex;
  align-items: center;
  justify-content: flex-end;
  border-bottom: 1px solid var(--lightest-gray);
}

.sort-select {
  position: relative;
  display: flex;
  align-items: center;

  margin: 0 10px;

  &:last-of-type {
    margin-right: 0;
  }

  select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    vertical-align: middle;
    background-color: var(--lightest-gray);
    border-radius: var(--border-radius);
    border: 0;
    overflow: hidden;
    padding: 5px;
    padding-right: 15px;
    cursor: pointer;
    outline: 0;
  }

  i {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
  }
}

.cards {
  padding: var(--page-padding);
  padding-bottom: var(--page-padding-bottom);
  display: grid;
  grid-template-columns: repeat(auto-fill, var(--width-small));
  grid-gap: 30px 20px;
  justify-content: space-between;
  width: 100%;

  &.loading {
    opacity: 0.5;
  }
}
</style>
