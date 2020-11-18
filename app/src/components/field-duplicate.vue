<template>
	<v-modal
		:title="
			$t('duplicating_field') +
				': ' +
				$helpers.formatField(fieldInfo.field, fieldInfo.collection)
		"
		:buttons="buttons"
		@save="saveField()"
		@close="$emit('close')"
	>
		<form class="options" @submit.prevent>
			<div class="options">
				<label>
					{{ $tc('collection', 1) }}
					<v-simple-select v-model="selectedCollection" required>
						<option
							v-for="collection in Object.keys(this.collections)"
							:key="collection"
							:value="collection"
							:selected="collection === selectedCollection"
						>
							{{ $helpers.formatTitle(collection) }}
						</option>
					</v-simple-select>
				</label>
			</div>
			<div class="options">
				<label>
					{{ $tc('field', 1) + ' ' + $t('name') }}
					<v-input
						v-model="field"
						required
						:value="field"
						:placeholder="fieldInfo.field"
						:icon-right="iconToShow.icon"
						:icon-right-color="iconToShow.color"
						:icon-right-tooltip="iconToShow.tooltip"
					/>
				</label>
				<p class="small-text">
					{{ $t('display_name') }}:
					<b>{{ $helpers.formatTitle(field || '...') }}</b>
				</p>
			</div>
		</form>
	</v-modal>
</template>

<script>
import { datatypes } from '../type-map';
import { cloneDeep } from 'lodash';

export default {
	name: 'VFieldDuplicate',
	props: {
		collectionInformation: {
			type: Object,
			required: true
		},
		fieldInformation: {
			type: Object,
			required: true
		}
	},
	data() {
		return {
			fieldInfo: Object.assign({}, this.fieldInformation),
			selectedCollection: this.collectionInformation.collection,
			field: null,
			saving: false
		};
	},
	computed: {
		databaseVendor() {
			return cloneDeep(this.$store.state.serverInfo.databaseVendor);
		},
		selectedDatatypeInfo() {
			return datatypes[this.databaseVendor][this.fieldInfo.datatype];
		},
		lengthDisabled() {
			if (this.selectedDatatypeInfo && this.selectedDatatypeInfo.length === true) {
				return false;
			}

			if (this.selectedDatatypeInfo && this.selectedDatatypeInfo.decimal === true) {
				return false;
			}

			return true;
		},
		canDuplicate() {
			if (this.field && this.isFieldValid && this.selectedCollection) {
				return true;
			}
			return false;
		},
		iconToShow() {
			if (!this.field) {
				return { icon: null, color: null };
			}
			if (this.isFieldValid) {
				return { icon: 'done', color: 'success' };
			}
			return {
				icon: 'error',
				color: 'danger',
				tooltip: this.$t('field_already_exists', { field: "'" + this.field + "'" })
			};
		},
		isFieldValid() {
			let isValid = true;
			Object.keys(this.collections[this.selectedCollection].fields).forEach(field => {
				if (field === this.field) {
					isValid = false;
				}
			});
			if (isValid) {
				return true;
			}
			return false;
		},
		collectionFieldCount() {
			return Object.keys(this.collections[this.selectedCollection].fields).length;
		},
		collections() {
			const collections = Object.assign({}, this.$store.state.collections);
			return Object.keys(collections)
				.filter(collection => collection.startsWith('directus_') === false)
				.reduce((obj, collection) => {
					obj[collection] = collections[collection];
					return obj;
				}, {});
		},
		buttons() {
			return {
				save: {
					disabled: !this.canDuplicate,
					text: this.$t('create'),
					loading: this.saving
				}
			};
		}
	},
	watch: {
		field(val) {
			// Based on https://gist.github.com/mathewbyrne/1280286
			this.field = val
				.toString()
				.toLowerCase()
				.replace(/\s+/g, '_') // Replace spaces with _
				.replace(/[^\w_]+/g, '') // Remove all non-word chars
				.replace(/__+/g, '_'); // Replace multiple _ with single _
		}
	},
	methods: {
		saveField() {
			this.saving = true;

			let fieldInfo = this.fieldInfo;
			fieldInfo.field = this.field;
			fieldInfo.sort = this.collectionFieldCount + 1;
			delete fieldInfo.id;
			delete fieldInfo.name;

			if (this.lengthDisabled === true) {
				delete fieldInfo.length;
			}

			const result = {
				fieldInfo,
				collection: this.selectedCollection
			};

			this.$emit('save', result);
		}
	}
};
</script>

<style lang="scss" scoped>
form.options {
	padding: 5% 10%;

	div.options {
		margin-bottom: 30px;

		&:last-of-type {
			margin-bottom: 0px;
		}
	}

	& label > .v-simple-select,
	& label > .v-input {
		margin-top: 10px;
	}

	.required {
		color: var(--blue-grey-900);
		vertical-align: super;
		font-size: 7px;
	}

	.small-text {
		margin-top: 4px;
		font-style: italic;
		font-size: 12px;
		line-height: 1.5em;
		color: var(--blue-grey-300);
		& b {
			font-weight: 600;
		}
	}
}
</style>
