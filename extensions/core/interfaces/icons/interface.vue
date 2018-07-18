<template>
  <div class="interface-icons">
    <v-input v-model="searchText" placeholder="Search an icon" :icon-right="value"/>
    <div class="icons-view" v-show="searchText.length === 0">
      <details 
      v-for="(icongroup,groupname) in icons"
      :key="(icongroup,groupname)"
      open>
        <summary>
          {{groupname}}
        </summary>
        <div>
          <button
            v-for="icon in icongroup"
            :key="icon"
            v-tooltip="$helpers.formatTitle(icon)"
            :class="{ active: value === icon}"
            @click="$emit('input', icon)">
            <i class="material-icons">{{ icon }}</i>
          </button>
        </div>
      </details>
    </div>
    <div class="search-view" v-if="searchText.length > 0">
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
  height: 320px;
  max-width: 320px;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  background-color: white;
  padding: 0.3em;

  input {
    width: auto;
    margin: 2px;
    padding: 10px;
    border: var(--input-border-width) solid var(--lighter-gray);
    border-radius: var(--border-radius);
    color: var(--gray);
    height: var(--input-height);
    transition: var(--fast) var(--transition);
    transition-property: color, border-color, padding;
    &:focus {
      color: var(--darker-gray);
      border-color: var(--accent);
      outline: 0;
    }
  }
  .icons-view {
    details {
      text-transform: uppercase;
      summary {
        margin: 5px 2px;
      }
      div {
        button {
          padding: 0.4em;
          transform: scale(1);
          transition: transform var(--fast) var(--transition-in);
          opacity: 0.5;
          &.active {
            opacity: 1;
          }
          &:hover {
            opacity: 1;
            transition: none;
            transform: scale(1.2);
            z-index: +1;
            box-shadow: var(--box-shadow);
          }
        }
      }
    }
  }
  .search-view {
    button {
      padding: 0.4em;
      transform: scale(1);
      transition: transform var(--fast) var(--transition-in);
      opacity: 0.5;
      &.active {
        opacity: 1;
      }
      &:hover {
        opacity: 1;
        transition: none;
        transform: scale(1.2);
        z-index: +1;
        box-shadow: var(--box-shadow);
      }
    }
  }
}
</style>

