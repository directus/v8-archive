<template>
  <div class="interface-icons">
    <v-input v-model="searchText" :placeholder="$t('interfaces-icons-search_placeholder')" :readonly="readonly" :icon-right="value" icon-left="search"/>
    <div class="icons-view" v-show="searchText.length === 0">
      <details
        v-for="(icongroup, groupname) in icons"
        :key="groupname"
        open>
        <summary>
          {{ $helpers.formatTitle(groupname) }}
        </summary>
        <div>
          <button
            v-for="icon in icongroup"
            :key="icon"
            v-tooltip="$helpers.formatTitle(icon)"
            :class="{ active: value === icon }"
            @click="$emit('input', icon)">
            <i class="material-icons">{{ icon }}</i>
          </button>
        </div>
      </details>
    </div>
    <div v-if="searchText.length > 0" class="search-view">
      <button
        v-for="icon in filteredArray"
        v-tooltip="$helpers.formatTitle(icon)"
        :key="icon"
        :class="{ active: value === icon}"
        @click="$emit('input', icon)">
        <i class="material-icons">{{ icon }}</i>
      </button>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import icons from "./icons.json";

export default {
  mixins: [mixin],
  data() {
    return {
      searchText: ""
    };
  },
  computed: {
    icons() {
      return icons;
    },
    iconsArray() {
      return this.$lodash.flatten(Object.values(this.icons));
    },
    filteredArray() {
      return this.iconsArray.filter(icon =>
        icon.includes(this.searchText.toLowerCase())
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-icons {
  display: flex;
  flex-direction: column;
  overflow-y: scroll;
  height: 334px;
  width: 100%;
  max-width: var(--width-normal);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  background-color: white;
  padding: 10px;

  .v-input {
    // color: var(--gray);
    // height: var(--input-height);
    // transition: var(--fast) var(--transition);
    // transition-property: color, border-color;
    position: sticky;
    top: 0;
    z-index: +1;

    &:focus {
      // color: var(--darker-gray);
      // border-color: var(--accent);
      // outline: 0;
    }
  }

  .icons-view {
    details {
      text-transform: uppercase;

      summary {
        margin: 20px 2px 5px;
        cursor: pointer;
        color: var(--gray);
        &:hover {
          color: var(--darker-gray);
        }
      }

      button {
        padding: 5px;
        // transform: scale(1);
        transition: color var(--slow) var(--transition);
        // opacity: 0.5;
        color: var(--lighter-gray);

        &.active,
        &.active:hover {
          // opacity: 1;
          color: var(--darker-gray);
        }

        &:hover {
          color: var(--dark-gray);
          // opacity: 1;
          // transition: none;
          // transform: scale(1.2);
          // z-index: +1;
          // box-shadow: var(--box-shadow);
        }
      }
    }
  }
  .search-view button {
    padding: 0.4em;
    // transform: scale(1);
    transition: color var(--slow) var(--transition);
    // opacity: 0.5;
    color: var(--lighter-gray);
    &.active {
      // opacity: 1;
      color: var(--darker-gray);
    }

    &:hover {
      color: var(--dark-gray);
      // opacity: 1;
      // transition: none;
      // transform: scale(1.2);
      // z-index: +1;
      // box-shadow: var(--box-shadow);
    }
  }
}
</style>
