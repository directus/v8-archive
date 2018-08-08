<template>
  <div class="interface-map">
    <div class="interface-map-wrap">
      <div 
        class="map" 
        id="directusMap" 
        :style="{width:options.width+'px',height:options.height+'px'}">
      <!-- Map Renders Here -->
      </div>

      <div class="map-actions">
        <!-- TODO: Add Geo Search Here -->
        <button 
          class="map-my-location" 
          @click="locateMe()">
          <i class="material-icons">my_location</i>
        </button>
      </div>

      <div class="map-details">
        <div class="map-location style-4">
          <span v-if="latlng">Latitude: {{latlng.lat}}</span>
          <span v-if="latlng">Longitude: {{latlng.lng}}</span>
        </div>
        <button 
          class="map-clear style-4" 
          @click="setValue()">Clear</button>
      </div>
    </div>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";
import leaflet from "leaflet";
import "leaflet/dist/leaflet.css";

export default {
  name: "interface-map",
  mixins: [mixin],
  data() {
    return {
      map: null,
      marker: null,
      latlng: {
        lat: null,
        lng: null
      },
      isLocating: false,
      mapPlaceholder: "directusMap"
    };
  },
  mounted() {
    this.init();
  },
  watch: {
    "options.theme"(newVal) {
      leaflet.tileLayer(newVal).addTo(this.map);
    },

    // Automatically update the Marker based on lat & long
    latlng(newVal) {
      this.setMarker(newVal);
    }
  },
  methods: {
    init() {
      //Set default values to emit.
      this.createMap({
        lat: this.options.lat,
        lng: this.options.lng
      });
    },

    createMap(latlng) {
      this.map = leaflet.map(this.mapPlaceholder, {
        center: latlng,
        zoom: this.options.zoom,
        maxZoom: this.options.maxZoom,
        zoomControl: false
      });

      /**
       * Set tileLayer
       * tileLayer defines the interface/theme of the map
       * There are serveral tileLayers available here: http://leaflet-extras.github.io/leaflet-providers/preview/
       */
      leaflet
        .tileLayer(this.options.theme, {
          attribution:
            '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        })
        .addTo(this.map);

      /**
       * Bind interaction method only in "input" mode
       * For "display" mode, interactions are not required.
       */
      this.setValue(latlng);
      this.bindMapInteractions();
    },

    /**
     * Handles Marker Positioning
     */
    setMarker(latlng) {
      if (this.marker) {
        if (latlng) {
          this.marker.setLatLng(latlng).setOpacity(1);
        } else {
          this.marker.setOpacity(0);
        }
      } else if (this.map) {
        // Create A Marker Instance
        let markerIcon = leaflet.icon({
          //? Should we use base64?
          iconUrl: this.markerSVG(),
          iconSize: [36, 36],
          iconAnchor: [18, 36]
        });
        // Set marker on the default position
        this.marker = leaflet
          .marker(latlng, {
            icon: markerIcon,
            draggable: true
          })
          .addTo(this.map);

        this.bindMarkerInteractions();
      }
    },

    /**
     * Always this function will emit the value.
     * Passing NULL will remove values & hide marker
     */
    setValue(latlng) {
      this.latlng = latlng;
      this.setMarker(latlng);
      this.$emit(
        "input",
        this.latlng ? JSON.parse(JSON.stringify(this.latlng)) : null
      );
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
    },

    markerSVG() {
      return `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#039be5" stroke-width="1" stroke="#0078b3" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>`;
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
  //This is fallback size. Generally this will be overwritten by default size provided in interface config.
  width: var(--width-normal);
  height: var(--width-normal);
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
