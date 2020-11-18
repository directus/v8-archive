<template>
	<div class="interface-icon">
		<v-input
			v-model="searchText"
			:placeholder="$t('interfaces.icon.search_placeholder')"
			:readonly="readonly"
			:icon-right="value"
			icon-right-color="input-background-color-active"
			icon-left="search"
		></v-input>
		<div v-show="searchText.length === 0" class="icons-view">
			<details
				v-for="(icongroup, groupname) in icons"
				:key="groupname"
				@toggle="handleIconGroupToggle(groupname)"
				:open="openIconGroups[groupname] ? 'open' : undefined"
			>
				<summary>{{ $helpers.formatTitle(groupname) }}</summary>
				<div v-if="openIconGroups[groupname]">
					<button
						v-for="icon in icongroup"
						:key="icon"
						type="button"
						:class="{ active: value === icon }"
						:disabled="readonly"
						@click="$emit('input', value === icon ? null : icon)"
					>
						<v-icon :name="icon" />
					</button>
				</div>
			</details>
		</div>
		<div v-if="searchText.length > 0" class="search-view">
			<button
				v-for="icon in filteredArray"
				:key="icon"
				v-tooltip="$helpers.formatTitle(icon)"
				type="button"
				:class="{ active: value === icon }"
				:disabled="readonly"
				@click="$emit('input', value === icon ? null : icon)"
			>
				<v-icon :name="icon" />
			</button>
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import icons from './icons.json';
import { flatten } from 'lodash';

export default {
	mixins: [mixin],
	data() {
		return {
			searchText: '',
			openIconGroups: {}
		};
	},
	computed: {
		icons() {
			return icons;
		},
		iconsArray() {
			return flatten(Object.values(this.icons));
		},
		filteredArray() {
			return this.iconsArray.filter(icon => icon.includes(this.searchText.toLowerCase()));
		}
	},
	methods: {
		handleIconGroupToggle(groupname) {
			this.$set(this.openIconGroups, groupname, !this.openIconGroups[groupname]);
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-icon {
	display: flex;
	flex-direction: column;
	overflow-y: scroll;
	height: 344px;
	width: 100%;
	max-width: var(--width-medium);
	border-radius: var(--border-radius);
	background-color: white;
	padding: 10px;
	background-color: var(--input-background-color-alt);
	border: var(--input-border-width) solid var(--input-border-color);

	.v-input {
		position: sticky;
		top: 0;
		z-index: +1;
	}

	details {
		font-size: 14px;

		summary {
			margin: 20px 2px 5px;
			cursor: pointer;
			color: var(--modal-tab-text-color);

			&:hover {
				color: var(--page-text-color);
			}
		}

		&[open] summary {
			color: var(--page-text-color);
		}
	}

	button {
		padding: 0.5em;
		transition: color var(--fast) var(--transition);
		color: var(--input-icon-color);
		max-width: 37px;

		&.active {
			color: var(--input-background-color-active);
		}

		&:hover {
			transition: none;
			color: var(--input-text-color);
		}
	}
	button[disabled='disabled'] {
		&:hover {
			cursor: not-allowed;
			color: var(--input-icon-color);
		}
	}
}
</style>
