<template>
  <div class="interface-icon">
    <v-input v-model="searchText" :placeholder="$t('interfaces-icon-search_placeholder')" :readonly="readonly" :icon-right="value" icon-left="search"></v-input>
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
            type="button"
            :key="icon"
            :class="{ active: value === icon }"
            :disabled="readonly"
            @click="$emit('input', value === icon ? null : icon)">
            <i class="material-icons">{{ icon }}</i>
          </button>
        </div>
      </details>
    </div>
    <div v-if="searchText.length > 0" class="search-view">
      <button
        v-for="icon in filteredArray"
        v-tooltip="$helpers.formatTitle(icon)"
        type="button"
        :key="icon"
        :class="{ active: value === icon}"
        :disabled="readonly"
        @click="$emit('input', value === icon ? null : icon)">
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
.interface-icon {
  display: flex;
  flex-direction: column;
  overflow-y: scroll;
  height: 334px;
  width: 100%;
  max-width: var(--width-medium);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  background-color: white;
  padding: 10px;

  .v-input {
    position: sticky;
    top: 0;
    z-index: +1;
  }

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
  }

  button {
    padding: 0.5em;
    transition: color var(--fast) var(--transition);
    color: var(--lighter-gray);
    max-width: 37px;

    &.active {
      color: var(--darker-gray);
    }

    &:hover {
      transition: none;
      color: var(--dark-gray);
    }
  }
  button[disabled="disabled"] {
    &:hover {
      cursor: not-allowed;
      color: var(--lighter-gray);
    }
  }
}
</style>
