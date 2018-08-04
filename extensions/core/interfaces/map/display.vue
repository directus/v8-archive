<template>
  <div class="readonly-map">
    <div class="interface-map-wrap">
      <div class="map-display" id="directusMapDisplay" :style="{width:options.width+'px',height:options.height+'px'}">
        <!-- Map Renders Here -->
      </div>
    </div>
  </div>
</template>

<script>
import mapMixin from "./map.js";

export default {
  name: "readonly-map",
  mixins: [mapMixin],
  data() {
    return {
      mapPlaceholder: "directusMapDisplay",
      mode: "display"
    };
  },
  watch: {
    //? Do we need to re-render map when "value" changes from interface debugger?
    value: function(newVal) {
      this.viewMap(newVal);
    }
  },
  mounted() {
    this.viewMap(this.value);
  },
  methods: {
    viewMap(latlng) {
      this.latlng = JSON.parse(latlng);
      if (this.latlng) {
        if (this.map) {
          this.map.setView(this.latlng);
        } else {
          this.createMap(JSON.parse(latlng));
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.map-display {
  width: 200px;
  height: 200px;
  z-index: 1;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
}
.interface-map-wrap {
  position: relative;
  display: inline-flex;
  flex-direction: column;
}
@media only screen and (max-width: 800px) {
  .interface-map-wrap {
    display: flex;
  }
  .map-display {
    width: 100% !important;
  }
}
</style>

