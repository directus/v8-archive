<template>
	<div class="interface-map">
		<div :class="{ 'map-readonly': readonly }" class="map">
			<div :id="name" class="map-container" :style="{ height: options.height + 'px' }">
				<!-- Map Renders Here -->
			</div>

			<div class="map-actions">
				<div v-if="options.address_to_code" class="address-input">
					<v-input v-model="placeName" placeholder="Enter address to geocode"></v-input>
					<button
						v-if="isInteractive"
						v-tooltip="$t('interfaces.map.address_location')"
						@click="getCoordinatesforPlaceName()"
					>
						<v-icon name="search" />
					</button>
				</div>

				<button
					v-if="isInteractive"
					v-tooltip="$t('interfaces.map.my_location')"
					class="map-my-location"
					@click="locateMe()"
				>
					<v-icon name="my_location" />
				</button>

				<button
					v-if="isInteractive"
					v-tooltip="$t('interfaces.map.clear_location')"
					class="clear-location"
					@click="setValue()"
				>
					<v-icon name="clear" />
				</button>
			</div>
		</div>

		<div class="map-coordinates">
			<span v-if="latlng">
				Latitude:
				<b>{{ latlng.lat }}</b>
				,&nbsp;
			</span>
			<span v-if="latlng">
				Longitude:
				<b>{{ latlng.lng }}</b>
			</span>
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import leaflet from 'leaflet';
import './leaflet.css';
import { debounce } from 'lodash';

export default {
	name: 'InterfaceMap',
	mixins: [mixin],
	data() {
		return {
			test: null,
			map: null,
			marker: null,
			latlng: null,
			isLocating: false,
			placeName: '',
			mapInteractions: [
				'boxZoom',
				'doubleClickZoom',
				'dragging',
				'keyboard',
				'scrollWheelZoom',
				'tap',
				'touchZoom'
			]
		};
	},
	computed: {
		isInteractive() {
			return !this.readonly;
		},
		accentColor() {
			return getComputedStyle(document.documentElement)
				.getPropertyValue('--blue-grey-900')
				.trim();
		},
		darkAccentColor() {
			return getComputedStyle(document.documentElement)
				.getPropertyValue('--blue-grey-900')
				.trim();
		}
	},
	watch: {
		'options.theme'(newVal) {
			leaflet.tileLayer(newVal).addTo(this.map);
		},

		readonly(status) {
			this.toggleMapInteractions(!status);
			this.toggleMarkerInteractions(!status);
			if (status) {
				this.unbindMapEvents();
				this.unbindMarkerEvents();
			} else {
				this.bindMapEvents();
				this.bindMarkerEvents();
			}
		},

		// Automatically update the Marker based on lat & long
		latlng(newVal) {
			this.setMarker(newVal);
		}
	},
	mounted() {
		this.init();
	},
	methods: {
		init() {
			let _latlng;
			/**
			 * If value is provided on initialization,
			 * map should be centered at lat/lng of value
			 * else it should center at provided default location.
			 */
			if (this.value) {
				_latlng = leaflet.latLng(this.value.lat, this.value.lng);
			} else {
				_latlng = leaflet.latLng(this.options.mapLat, this.options.mapLng);
			}
			this.createMap(_latlng);
		},

		createMap(latlng) {
			this.map = leaflet.map(this.name, {
				center: latlng,
				zoom: this.options.defaultZoom,
				maxZoom: this.options.maxZoom,
				zoomControl: true
			});

			/**
			 * Set tileLayer
			 * tileLayer defines the interface/theme of the map
			 * There are serveral tileLayers available here: http://leaflet-extras.github.io/leaflet-providers/preview/
			 */
			leaflet
				.tileLayer(this.options.theme, {
					attribution: '&copy; <a href="https://carto.com/">Carto</a>'
				})
				.addTo(this.map);

			/**
			 * Render marker only if value is set.
			 */
			this.value ? this.setValue(this.value) : '';

			/**
			 * Bind interaction method only in "input" mode
			 * For "display" mode, interactions are not required.
			 */
			this.isInteractive ? this.bindMapEvents() : this.unbindMapEvents();
		},

		/**
		 * Handles Marker Positioning
		 */
		setMarker(latlng) {
			if (this.marker) {
				//Hide marker if latlng is provided NULL
				if (latlng) {
					this.marker.setLatLng(latlng).setOpacity(1);
				} else {
					this.marker.setOpacity(0);
				}
			} else {
				// Create A Marker Instance
				let markerIcon = leaflet.icon({
					iconUrl: this.markerSVG(),
					iconSize: [36, 36],
					iconAnchor: [18, 36]
				});
				// Set marker on the position
				this.marker = leaflet
					.marker(latlng, {
						icon: markerIcon,
						draggable: this.isInteractive
					})
					.addTo(this.map);

				if (this.isInteractive) {
					this.bindMarkerEvents();
					this.toggleMapInteractions(true);
				}
			}
		},

		/**
		 * Always this function will emit the value.
		 * Passing NULL will remove values & hide marker
		 */
		setValue(latlng) {
			this.latlng = latlng;
			this.$emit('input', this.latlng ? JSON.parse(JSON.stringify(this.latlng)) : null);
		},

		toggleMarkerInteractions(status) {
			status ? this.marker.dragging.enable() : this.marker.dragging.disable();
		},

		unbindMarkerEvents() {
			this.marker.off('drag');
		},

		bindMarkerEvents() {
			// Handle drag event of marker.
			this.marker.on(
				'drag',
				debounce(e => {
					this.setValue(e.latlng);
				}, 100)
			);
		},

		toggleMapInteractions(status) {
			/**
			 * Loop through all the possible interaction option & set status
			 */
			this.mapInteractions.forEach(item => {
				if (this.map[item]) {
					status ? this.map[item].enable() : this.map[item].disable();
				}
			});
		},

		unbindMapEvents() {
			this.map.off('click');
		},

		bindMapEvents() {
			/**
			 * Handle click event on the map.
			 * This will place marker on clicked point.
			 */
			this.map.on('click', e => {
				this.setValue(e.latlng);
			});

			// User location detection events
			// Location Error
			this.map.on('locationerror', result => {
				this.$events.emit('error', {
					notify:
						//This error codes are returned from leaflet library.
						result.code == 1
							? this.$t('interfaces.map.user_location_error_blocked')
							: this.$t('interfaces.map.user_location_error'),
					error: result
				});
				this.isLocating ? this.$store.dispatch('loadingFinished', this.isLocating) : '';
			});

			//Location Success
			this.map.on('locationfound', result => {
				this.isLocating ? this.$store.dispatch('loadingFinished', this.isLocating) : '';
				this.setValue(result.latlng);
			});
		},

		//Find User Location
		locateMe() {
			this.isLocating = this.isLocating || this.$helpers.shortid.generate();
			this.$store.dispatch('loadingStart', {
				id: this.isLocating
			});
			this.map.locate({ setView: true, maxZoom: this.options.maxZoom });
		},

		markerSVG() {
			// Replace # with %23 so svg also works in Firefox
			return `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="${this.accentColor.replace(
				'#',
				'%23'
			)}" stroke-width="1" stroke="${this.darkAccentColor.replace(
				'#',
				'%23'
			)}" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>`;
		},

		// Place name for geocode lookup on openstreetmap database via Nominatim, Returns coordinates in [lat,lon]
		getCoordinatesforPlaceName() {
			this.$store.dispatch('loadingStart', {
				id: this.isLocating
			});
			this.$axios
				.get(
					`https://nominatim.openstreetmap.org/search?q=${encodeURI(
						this.placeName
					)}&format=geojson&addressdetails=1&limit=1`
				)
				.then(response => {
					if (response.status === 200) {
						if (!response.data.features[0]) {
							this.$events.emit('error', {
								notify: this.$t('interfaces.map.address_to_code_error'),
								error: 'result'
							});
						} else {
							let coordArray = response.data.features[0].geometry.coordinates;
							let coordinates = {
								lat: coordArray[1],
								lng: coordArray[0]
							};
							this.setValue(coordinates);
							this.map.panTo(new leaflet.LatLng(coordArray[1], coordArray[0]));
							this.$store.dispatch('loadingFinished', this.isLocating);
						}
					}
				})
				.catch(err => {
					this.$events.emit('error', {
						notify: err,
						error: 'result'
					});
					this.$store.dispatch('loadingFinished', this.isLocating);
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-map {
	overflow-x: auto;
	overflow-y: hidden;
	position: relative;
}

.map {
	transition: all var(--fast) var(--transition);
	position: relative;
	display: flex;
	flex-direction: column;
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	&:hover {
		border-color: var(--input-border-color-hover);
	}
}

.map-container {
	z-index: 1;
	width: 100%;
	//This is fallback size. Generally this will be overwritten by default size provided in interface config.
	height: var(--width-medium);
}

.map-actions {
	position: absolute;
	display: flex;
	width: 100%;
	top: 20px;
	left: 0px;
	padding: 0 20px;
	z-index: 2;
}

.address-input {
	display: flex;

	.v-input {
		width: 250px;
		margin-right: 8px;
	}

	button {
		transition: all var(--fast) var(--transition);
		width: 44px;
		height: 44px;
		border-radius: var(--border-radius);
		color: var(--white);
		background: var(--blue-grey-200);
		margin-right: 8px;
		&:hover {
			background: var(--blue-grey-300);
		}
	}
}

.map-my-location {
	transition: var(--fast) var(--transition) color;
	height: 44px;
	width: 44px;
	border-radius: var(--border-radius);
	color: var(--white);
	background: var(--blue-grey-200);
	margin-right: 8px;

	&:hover {
		background: var(--blue-grey-300);
	}
}

.clear-location {
	transition: var(--fast) var(--transition) color;
	height: 44px;
	width: 44px;
	border-radius: var(--border-radius);
	color: var(--white);
	background: var(--blue-grey-200);
	margin-right: 8px;

	&:hover {
		background: var(--blue-grey-300);
	}
}

.map-coordinates {
	position: absolute;
	bottom: 2px;
	left: 2px;
	z-index: 1;
	padding: 4px 8px 4px 4px;
	background-color: rgba(255, 255, 255, 0.6);
	border-radius: 0 var(--border-radius) 0 0;
	span {
		color: var(--blue-grey-300);
		text-transform: initial;
		font-style: italic;
	}
}

//Read Only Map
.map-readonly {
	.map-container {
		filter: grayscale(100%);
		opacity: 0.8;
	}
}

@media only screen and (max-width: 800px) {
	.map {
		display: flex;
	}
}
</style>
