<template>
  <div class="interface-icons">
    <input v-model="searchText">
    <div class="icons-view" v-show="searchText.length === 0">
      <details v-for="(icongroup,groupname) in icons" open>
        <summary>
          {{groupname}}
        </summary>
        <div>
          <button
            v-for="icon in icongroup"
            :key="icon"
            :class="{ active: value === icon}"
            @click="$emit('input', icon)">
            <i class="material-icons">{{ icon }}</i>
          </button>
        </div>
      </details>
    </div>
    <div v-if="searchText.length > 0">
      <button
        v-for="icon in filteredArray"
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
      return this.iconsArray.filter(icon => icon.includes(this.searchText));
    }
  }
};
</script>

<style lang="scss" scoped>
.icons-view {
  overflow-y: scroll;
  height: 20px;
}
</style>

