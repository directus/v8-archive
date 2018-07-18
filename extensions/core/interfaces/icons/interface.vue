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
/*
@REQUIREMENTS
It should show every icon in the material icon set that is available for the user.
The user should search through the icons and when the icon is set, it will be obviously know to the user that the icon is set (active).

@TODO
V create a scraper for all the material design icons.
V make an object with all the material design names with value.
V The object should be created within the group. So every group has it's icons.
- make an array that has every material design icon in the application.
- loop through the object and fill the array with the values.
V use .filter() and .includes() the user input variable.
V Show every material
*/

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

