<template>
	<div class="readonly-map">
		<v-icon v-tooltip="location" :class="value ? '' : 'empty'" name="place" />
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	name: 'ReadonlyMap',
	mixins: [mixin],
	computed: {
		location() {
			let _tooltip = '';
			if (this.value) {
				let _value = JSON.parse(JSON.stringify(this.value));
				_tooltip = `
        <table class="map-value-tooltip">
          <tr>
            <td>Latitude</td>
            <td>${_value.lat}</td>
          </tr>
          <tr>
            <td>Longitude</td>
            <td>${_value.lng}</td>
          </tr>
        </table>`;
			} else {
				_tooltip = this.$t('interfaces.map.no_location');
			}
			return _tooltip;
		}
	}
};
</script>

<style lang="scss" scoped>
i {
	cursor: help;
	&.empty {
		color: var(--empty-value);
	}
}
</style>

<style lang="scss">
.map-value-tooltip {
	border-collapse: collapse;
	tr + tr {
		border-top: 1px solid var(--blue-grey-600);
	}
	td {
		padding: 4px 8px;
	}
}
</style>
