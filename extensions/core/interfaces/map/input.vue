<template>
  <div class="interface-map">
    <div class="interface-map-wrap">
      <div class="map" id="directusMap" :style="{width:options.width+'px',height:options.height+'px'}">
      <!-- Map Renders Here -->
      </div>

      <div class="map-actions">
        <!-- TODO: Add Geo Search Here -->
        <button class="map-my-location" @click="locateMe()">
          <i class="material-icons">my_location</i>
        </button>
      </div>

      <div class="map-details">
        <div class="map-location style-4">
          <span v-if="latlng">Latitude: {{latlng.lat}}</span>
          <span v-if="latlng">Longitude: {{latlng.lng}}</span>
        </div>
        <button class="map-clear style-4" @click="setValue()">Clear</button>
      </div>
    </div>
  </div>
</template>

<script>
import mapMixin from "./map.js";

export default {
  name: "interface-map",
  mixins: [mapMixin],
  data() {
    return {
      isLocating: false,
      mapPlaceholder: "directusMap",
      mode: "input"
    };
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      //Set default values to emit.
      this.createMap({
        lat: this.options.lat,
        lng: this.options.lng
      });
    },
    bindMarkerInteractions() {
      // Handle drag event of marker.
      this.marker.on(
        "drag",
        _.debounce(e => {
          this.setValue(e.latlng);
        }, 100)
      );
    },

    bindMapInteractions() {
      /**
       * Handle click event on the map.
       * This will place marker on clicked point.
       */
      this.map.on("click", e => {
        this.setValue(e.latlng);
      });

      // User location detection events
      // Location Error
      this.map.on("locationerror", result => {
        this.$events.emit("error", {
          notify:
            //This error codes are returned from leaflet library.
            result.code == 1
              ? this.$t("interfaces-map-user_location_error_blocked")
              : this.$t("interfaces-map-user_location_error"),
          error: result
        });
        this.isLocating
          ? this.$store.dispatch("loadingFinished", this.isLocating)
          : "";
      });

      //Location Success
      this.map.on("locationfound", result => {
        this.isLocating
          ? this.$store.dispatch("loadingFinished", this.isLocating)
          : "";
        this.setValue(result.latlng);
      });
    },

    //Find User Location
    locateMe() {
      this.isLocating = this.isLocating || this.$helpers.shortid.generate();
      this.$store.dispatch("loadingStart", {
        id: this.isLocating
      });
      this.map.locate({ setView: true, maxZoom: this.options.maxZoom });
    }
  }
};
</script>

<style lang="scss" scoped>
.interface-map {
  overflow-x: auto;
  overflow-y: hidden;
}
.interface-map-wrap {
  position: relative;
  display: inline-flex;
  flex-direction: column;
}
.map {
  z-index: 1;
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
}

.map-actions {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 2;
}
.map-my-location {
  height: 40px;
  width: 40px;
  border-radius: var(--border-radius);
  color: var(--accent);
  box-shadow: var(--box-shadow);
  background: #fff;
  &:hover {
    box-shadow: var(--box-shadow-accent);
  }
}

.map-details {
  display: flex;
  margin-top: 4px;
  justify-content: space-between;
}
.map-location {
  span {
    text-transform: initial;
    margin-right: 20px;
    font-style: italic;
  }
}
.map-clear {
  text-transform: initial;
  color: var(--accent);
  font-style: italic;
}

@media only screen and (max-width: 800px) {
  .interface-map-wrap {
    display: flex;
  }
  .map {
    width: 100% !important;
  }
}
</style>
