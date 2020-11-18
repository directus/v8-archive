<template>
	<div class="interface-debugger">
		<v-header
			:breadcrumb="links"
			:icon-link="`/${currentProjectKey}/settings/interfaces`"
			settings
		/>

		<label class="type-label">Dummy Label</label>

		<div :style="{ width: width + 'px' }" class="interface">
			<v-ext-input
				:id="id"
				v-model="value"
				:name="id"
				:type="type"
				:length="length"
				:readonly="readonly"
				:required="required"
				:loading="loading"
				:options="options"
				:new-item="newItem"
				:relation="relation"
				:fields="fields"
				:values="values"
			/>
		</div>

		<form @submit.prevent>
			<fieldset>
				<legend class="type-label">Output</legend>

				<label for="value" class="type-label">Value</label>
				<v-input id="value" v-model="value" type="text" class="value" />
				<p class="type-note">The value saved into the database</p>

				<label class="type-label">Display</label>
				<div class="listing">
					<v-ext-display
						v-model="value"
						:interface-type="id"
						:name="id"
						:type="type"
						:length="length"
						:readonly="readonly"
						:required="required"
						:loading="loading"
						:options="options"
						:new-item="newItem"
						:relation="relation"
					/>
				</div>
				<p class="type-note">Appearance on the Items Page (eg: Tabular)</p>
			</fieldset>
			<fieldset>
				<legend class="type-label">Options</legend>
				<div
					v-for="(option, optionID) in extension.options"
					:key="optionID"
					class="options"
				>
					<label :for="optionID" class="type-label">{{ option.name }}</label>
					<p v-if="options.comment" v-html="$helpers.snarkdown(option.comment)" />
					<v-ext-input
						:id="option.interface"
						v-model="options[optionID]"
						:name="optionID"
						:type="option.type"
						:length="option.length"
						:readonly="option.readonly"
						:required="option.required"
						:loading="option.loading"
						:options="option.options"
					/>
				</div>
			</fieldset>
			<fieldset>
				<legend class="type-label">Settings</legend>

				<div class="settings">
					<label for="type" class="type-label">Type</label>
					<v-simple-select id="type" v-model="type" class="small">
						<option v-for="type in extension.types" :key="type" :value="type">
							{{ type }}
						</option>
					</v-simple-select>
					<p class="type-note">Allowed datatypes this interface supports</p>
				</div>

				<div class="settings">
					<label for="length" class="type-label">Length</label>
					<v-input id="length" v-model="length" type="number" class="length" :min="0" />
					<p class="type-note">Database length for the column</p>
				</div>

				<div class="settings">
					<label for="collection" class="type-label">Collection</label>
					<v-input id="collection" v-model="collection" class="value" />
					<p class="type-note">The parent collection for this field</p>
				</div>

				<div class="settings">
					<v-checkbox
						id="readonly"
						v-model="readonly"
						value="readonly"
						class="checkbox"
						type="checkbox"
					/>
					<label for="readonly" class="inline type-label">Read only</label>
					<p class="type-note">Toggle the readonly/disabled state</p>
				</div>

				<div class="settings">
					<v-checkbox
						id="required"
						v-model="required"
						value="required"
						class="checkbox"
						type="checkbox"
					/>
					<label for="required" class="inline type-label">Required</label>
					<p class="type-note">Toggle the required state</p>
				</div>

				<div class="settings">
					<v-checkbox
						id="loading"
						v-model="loading"
						value="loading"
						class="checkbox"
						type="checkbox"
					/>
					<label for="loading" class="inline type-label">Loading</label>
					<p class="type-note">Toggle the loading state (Beta)</p>
				</div>
			</fieldset>
			<fieldset>
				<legend class="type-label">Relation</legend>

				<div class="relation">
					<div class="settings">
						<label for="collection_many" class="type-label">Collection Many</label>
						<v-input
							id="collection_many"
							v-model="relation.collection_many"
							type="text"
							class="value"
						/>
					</div>

					<div class="settings">
						<label for="field_many" class="type-label">Field Many</label>
						<v-input
							id="field_many"
							v-model="relation.field_many"
							type="text"
							class="value"
						/>
					</div>

					<div class="settings">
						<label for="collection_one" class="type-label">Collection One</label>
						<v-input
							id="collection_one"
							v-model="relation.collection_one"
							type="text"
							class="value"
						/>
					</div>

					<div class="settings">
						<label for="field_one" class="type-label">Field One</label>
						<v-input
							id="field_one"
							v-model="relation.field_one"
							type="text"
							class="value"
						/>
					</div>

					<div class="settings">
						<label for="junction_field" class="type-label">Junction Field</label>
						<v-input
							id="junction_field"
							v-model="relation.junction_field"
							type="text"
							class="value"
						/>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend class="type-label">Misc.</legend>

				<div class="misc">
					<v-checkbox
						id="new"
						v-model="newItem"
						value="newItem"
						class="checkbox"
						type="checkbox"
					/>
					<label for="new" class="inline type-label">New item</label>
					<p class="type-note">Toggle viewing between New and Edit</p>
				</div>
			</fieldset>
		</form>
		<v-info-sidebar wide>
			<span class="type-note">No settings</span>
		</v-info-sidebar>
	</div>
</template>

<script>
import mapping, { datatypes } from '../../type-map';
import { mapState } from 'vuex';
import { mapValues } from 'lodash';

export default {
	name: 'InterfaceDebugger',
	metaInfo() {
		return {
			title: 'Interface Debugger'
		};
	},
	props: {
		id: {
			type: String,
			required: true
		}
	},
	data() {
		return {
			type: null,
			length: null,
			value: null,
			readonly: false,
			required: false,
			loading: false,
			options: {},
			width: 2000,
			newItem: false,
			collection: 'members',
			relation: {
				field_many: 'favorites',
				field_one: 'members',
				collection_many: 'members',
				collection_one: 'movies',
				junction_field: ''
			},
			customFields: {
				name: {
					collection: 'movies',
					default_value: null,
					field: 'name',
					group: null,
					hidden_detail: false,
					hidden_browse: false,
					id: 154,
					interface: 'text-input',
					length: '100',
					locked: false,
					managed: true,
					name: 'Name',
					note: null,
					options: null,
					primary_key: false,
					readonly: false,
					relation: null,
					required: false,
					signed: null,
					sort: '10',
					translation: null,
					type: 'VARCHAR',
					validation: null,
					width: 4
				},
				director: {
					collection: 'movies',
					default_value: null,
					field: 'director',
					group: null,
					hidden_detail: false,
					hidden_browse: false,
					id: 161,
					interface: 'text-input',
					length: '100',
					locked: false,
					managed: true,
					name: 'Director',
					note: null,
					options: null,
					primary_key: false,
					readonly: false,
					relation: null,
					required: false,
					signed: null,
					sort: '9',
					translation: null,
					type: 'VARCHAR',
					validation: null,
					width: 4
				}
			},
			customValues: {
				id: 123,
				name: 'Directus The Movie',
				director: 'Ben Spielberg',
				preview: null
			}
		};
	},
	computed: {
		...mapState(['currentProjectKey']),
		links() {
			return [
				{
					name: this.$t('settings'),
					path: `/${this.currentProjectKey}/settings`
				},
				{
					name: this.$tc('interface', 2),
					path: `/${this.currentProjectKey}/settings/interfaces`
				},
				{
					name: this.extension.name,
					path: `/${this.currentProjectKey}/settings/interfaces/${this.id}`
				}
			];
		},
		extension() {
			return this.$store.state.extensions.interfaces[this.id];
		},
		fields() {
			return {
				...this.customFields,
				[this.id]: {
					type: this.type,
					length: this.length,
					value: this.value,
					readonly: this.readonly,
					required: this.required,
					loading: this.loading,
					options: this.options,
					width: this.width,
					newItem: this.newItem,
					relation: this.relation,
					collection: this.collection
				}
			};
		},
		values() {
			return {
				...this.customValues,
				[this.id]: this.value
			};
		}
	},
	watch: {
		id() {
			this.hydrate();
		},
		type(type) {
			if (type) {
				const { databaseVendor } = this.$store.state.serverInfo;
				const dbType = mapping[this.type][databaseVendor].default;

				const dbTypeInfo = datatypes[databaseVendor][dbType];

				if (dbTypeInfo.length) {
					this.length = dbTypeInfo.defaultLength;
				}
			}
		}
	},
	created() {
		this.hydrate();
	},
	methods: {
		hydrate() {
			// Set type to the first datatype available in the meta info
			this.type = this.extension.types[0];

			// Populate the options with the default values
			const defaults = mapValues(this.extension.options, settings =>
				settings.default === undefined ? null : settings.default
			);

			this.options = defaults;
		}
	}
};
</script>

<style scoped lang="scss">
.interface-debugger {
	padding: var(--page-padding-top) var(--page-padding) var(--page-padding-bottom);
}

form {
	margin-top: var(--form-vertical-gap);
}

label.type-label {
	margin-top: var(--form-vertical-gap);
	margin-bottom: var(--input-label-margin);
}

.type-note {
	margin-top: var(--input-note-margin);
}

.checkbox {
	width: auto;
	display: inline-block;
	vertical-align: middle;
}

.interface {
	max-width: 100%;
}

.value {
	max-width: 300px;
}

.listing {
	max-width: 100%;
}

.length {
	max-width: 140px;
}

.inline {
	display: inline-block;
}

label {
	margin-bottom: var(--input-label-margin);
	width: max-content;
}

fieldset {
	border-top: 2px solid var(--blue-grey-50);
	margin: 40px 0;

	legend {
		color: var(--blue-grey-200);
		padding: 10px 20px;
		text-align: center;
	}

	p {
		color: var(--blue-grey-300);
		padding-bottom: 10px;
		font-style: italic;
		max-width: 460px;
	}
}

output {
	background-color: white;
	padding: 10px;
	margin: 10px 0;
	font-family: monospace;
	display: block;
}

.relation {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	grid-gap: 0 20px;
}
</style>
