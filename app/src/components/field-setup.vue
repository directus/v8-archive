<template>
	<v-modal
		:title="existing ? $t('update_field') + ': ' + displayName : $t('create_field')"
		:tabs="tabs"
		:active-tab="activeTab"
		:buttons="buttons"
		@tab="activeTab = $event"
		@next="nextTab"
		@close="$emit('close')"
	>
		<template slot="interface">
			<h1 v-if="!existing" class="type-heading-medium">
				{{ $t('field_setup_interface') }}
			</h1>
			<v-notice v-if="interfaceName" color="gray" class="currently-selected">
				{{ $t('currently_selected', { thing: interfaces[interfaceName].name }) }}
			</v-notice>
			<v-input
				v-else
				v-model="interfaceFilter"
				type="text"
				placeholder="Find an interface..."
				class="interface-filter"
				icon-left="search"
				autofocus
			/>
			<div v-if="!interfaceFilter">
				<v-details
					v-for="group in interfacesPopular"
					:key="group.title"
					:title="group.title"
					:open="true"
				>
					<div class="interfaces">
						<article
							v-for="ext in group.interfaces"
							:key="group.title + '-' + ext.id"
							:class="{ active: interfaceName === ext.id }"
							class="interface"
							@click="setInterface(ext.id)"
						>
							<div class="header">
								<v-icon :name="ext.icon || 'category'" x-large color="--white" />
							</div>
							<div class="body">
								<h2>{{ ext.name }}</h2>
								<p>{{ interfaceSubtitles(ext) }}</p>
							</div>
						</article>
					</div>
				</v-details>
			</div>
			<div>
				<div class="interfaces">
					<article
						v-for="ext in interfacesFiltered"
						:key="'all-' + ext.id"
						:class="{ active: interfaceName === ext.id }"
						class="interface"
						@click="setInterface(ext.id)"
					>
						<div class="header">
							<v-icon :name="ext.icon || 'category'" x-large color="--white" />
						</div>
						<div class="body">
							<h2>{{ ext.name }}</h2>
							<p>{{ interfaceSubtitles(ext) }}</p>
						</div>
					</article>
				</div>
			</div>
		</template>

		<template v-if="interfaceName" slot="schema">
			<h1 v-if="!existing" class="type-heading-medium">
				{{ $t('field_setup_schema') }}
			</h1>
			<form class="schema" @submit.prevent>
				<div class="name">
					<label>
						<span class="type-label">{{ $t('name') }}</span>
						<v-icon class="required" name="star" color="input-required-color" sup />
						<v-input
							autofocus
							type="text"
							:placeholder="$t('db_column_name')"
							class="name-input"
							:disabled="existing"
							:icon-right="iconToShow.icon"
							:icon-right-color="iconToShow.color"
							:icon-right-tooltip="iconToShow.tooltip"
							:value="field"
							@input="field = slug($event, { replacement: '_', lower: true }).trim()"
						/>
						<p class="type-note">
							{{ $t('display_name') }}:
							<b>{{ $helpers.formatTitle(field || '...') }}</b>
						</p>
					</label>
					<label>
						<span class="type-label">{{ $t('default_value') }}</span>
						<v-input
							v-model="default_value"
							type="text"
							placeholder="NULL"
							:disabled="type === 'o2m' || type === 'translation'"
						/>
					</label>
				</div>
				<label>
					<span class="type-label">{{ $t('note') }}</span>
					<v-input v-model="note" type="text" :placeholder="$t('add_note')" />
				</label>

				<div class="toggles">
					<v-switch
						v-model="required"
						:disabled="fieldInfo.primary_key || type === 'alias'"
						:label="$t('required')"
					/>

					<v-switch v-model="readonly" :label="$t('readonly')" />
				</div>

				<details class="advanced" :open="existing">
					<summary>{{ $t('advanced_options') }}</summary>
					<div class="advanced-form">
						<label>
							<span class="type-label">{{ $t('field_type') }}</span>
							<v-simple-select v-model="type">
								<option
									v-for="typeOption in availableFieldTypes"
									:key="typeOption"
									:value="typeOption"
									:selected="type === typeOption"
								>
									{{ $helpers.formatTitle(typeOption) }}
								</option>
							</v-simple-select>
							<small class="type-note">{{ fieldTypeDescription }}</small>
						</label>
						<label>
							<span class="type-label">
								{{
									$t('db_datatype', {
										db: $helpers.formatTitle(databaseVendor)
									})
								}}
							</span>
							<v-simple-select v-model="datatype">
								<option
									v-for="typeOption in availableDatatypes"
									:key="typeOption"
									:value="typeOption"
									:selected="datatype === typeOption"
								>
									{{ typeOption }}
								</option>
							</v-simple-select>
							<small class="type-note">
								{{ selectedDatatypeInfo && $t(selectedDatatypeInfo.description) }}
							</small>
						</label>
						<label>
							<span class="type-label">{{ $t('length') }}</span>
							<v-input
								:type="
									selectedDatatypeInfo && selectedDatatypeInfo.decimal
										? 'string'
										: 'number'
								"
								:value="lengthDisabled ? null : length"
								:disabled="lengthDisabled"
								:placeholder="
									lengthDisabled ? $t('length_disabled_placeholder') : ''
								"
								@input="length = $event"
							/>
						</label>
						<label>
							<span class="type-label">{{ $t('validation') }}</span>
							<v-input v-model="validation" type="text" :placeholder="$t('regex')" />
						</label>

						<v-switch
							v-model="primary_key"
							:disabled="primaryKeyDisabled"
							:label="$t('primary_key')"
						/>
						<v-switch v-model="unique" :label="$t('unique')" />
						<v-switch v-model="hidden_detail" :label="$t('hidden_detail')" />
						<v-switch v-model="hidden_browse" :label="$t('hidden_browse')" />
						<v-switch v-if="isNumeric" v-model="signed" :label="$t('signed')" />

						<label class="translation">
							<span class="type-label">{{ $t('translation') }}</span>
							<v-ext-input
								id="repeater"
								v-model="translation"
								name="translation"
								type="json"
								width="full"
								:options="{
									fields: [
										{
											field: 'locale',
											type: 'string',
											interface: 'language',
											options: {
												limit: true
											},
											width: 'half'
										},
										{
											field: 'translation',
											type: 'string',
											interface: 'text-input',
											options: {
												placeholder: $t('translated_field_name')
											},
											width: 'half'
										}
									]
								}"
							/>
						</label>
					</div>
				</details>
			</form>
		</template>

		<template v-if="selectedInterfaceInfo && relation" slot="relation">
			<h1 v-if="!existing" class="type-heading-medium">
				{{ $t('field_setup_relation') }}
			</h1>

			<form v-if="relation === 'm2o'" class="single">
				<span class="type-label">{{ $t('this_collection') }}</span>

				<v-simple-select class="select" :value="relationInfo.collection_many" disabled>
					<option selected :value="collectionInfo.collection">
						{{ collectionInfo.collection }}
					</option>
				</v-simple-select>

				<v-simple-select class="select" :value="relationInfo.field_many" disabled>
					<option selected :value="field">{{ field }}</option>
				</v-simple-select>

				<v-icon name="arrow_backward" />

				<span class="type-label">{{ $t('related_collection') }}</span>

				<v-simple-select v-model="relationInfo.collection_one" class="select">
					<optgroup :label="$tc('collection', 2)">
						<option
							v-for="collection in collectionsGrouped.user"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
					<optgroup label="Directus">
						<option
							v-for="collection in collectionsGrouped.system"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
				</v-simple-select>

				<v-simple-select
					class="select"
					:value="primaryKeyFieldByCollection(relationInfo.collection_one).field"
					disabled
				>
					<option
						selected
						:value="primaryKeyFieldByCollection(relationInfo.collection_one).field"
					>
						{{ primaryKeyFieldByCollection(relationInfo.collection_one).field }}
					</option>
				</v-simple-select>
			</form>

			<form v-if="relation === 'o2m'" class="single">
				<span class="type-label">{{ $t('this_collection') }}</span>

				<v-simple-select class="select" :value="collectionInfo.collection" disabled>
					<option selected :value="collectionInfo.collection">
						{{ collectionInfo.collection }}
					</option>
				</v-simple-select>

				<v-simple-select class="select" :value="primaryKeyField.field" disabled>
					<option selected :value="primaryKeyField.field">
						{{ primaryKeyField.field }}
					</option>
				</v-simple-select>

				<v-icon name="arrow_forward" />

				<span class="type-label">{{ $t('related_collection') }}</span>

				<v-simple-select v-model="relationInfo.collection_many" class="select">
					<optgroup :label="$tc('collection', 2)">
						<option
							v-for="collection in collectionsGrouped.user"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
					<optgroup label="Directus">
						<option
							v-for="collection in collectionsGrouped.system"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
				</v-simple-select>

				<v-simple-select v-model="relationInfo.field_many" class="select">
					<option
						v-for="{ field } in fieldsNonSystem(relationInfo.collection_many)"
						:key="field"
						:value="field"
					>
						{{ field }}
					</option>
				</v-simple-select>
			</form>

			<form v-if="relation === 'm2m'" class="full">
				<p class="type-label">{{ $t('this_collection') }}</p>

				<v-simple-select class="select" :value="collectionInfo.collection" disabled>
					<option selected :value="collectionInfo.collection">
						{{ collectionInfo.collection }}
					</option>
				</v-simple-select>

				<v-simple-select class="select" :value="primaryKeyField.field" disabled>
					<option selected :value="primaryKeyField.field">
						{{ primaryKeyField.field }}
					</option>
				</v-simple-select>

				<v-icon name="arrow_forward" />

				<p class="type-label">{{ $t('junction_collection') }}</p>

				<v-simple-select v-if="!createM2Mjunction" v-model="junctionNameM2M" class="select">
					<optgroup :label="$tc('collection', 2)">
						<option
							v-for="collection in collectionsGrouped.user"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
					<optgroup label="Directus">
						<option
							v-for="collection in collectionsGrouped.system"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
				</v-simple-select>

				<v-input
					v-if="createM2Mjunction"
					v-model="junctionNameM2M"
					class="select"
					type="text"
					:placeholder="
						autoM2Msuggestion(
							collectionInfo.collection,
							relationInfoM2M[currentM2MIndex == 0 ? 1 : 0].collection_one
						)
					"
				/>

				<v-simple-select
					v-if="!createM2Mjunction"
					v-model="relationInfoM2M[currentM2MIndex].field_many"
					class="select"
				>
					<option
						v-for="{ field } in fields(relationInfoM2M[0].collection_many)"
						:key="field"
						:value="field"
					>
						{{ field }}
					</option>
				</v-simple-select>

				<v-input
					v-if="createM2Mjunction"
					v-model="relationInfoM2M[currentM2MIndex].field_many"
					class="select"
					type="text"
					:placeholder="autoM2Msuggestion(collectionInfo.collection, 'id')"
				/>

				<v-simple-select
					v-if="!createM2Mjunction"
					v-model="relationInfoM2M[currentM2MIndex === 0 ? 1 : 0].field_many"
					class="select"
				>
					<option
						v-for="{ field } in fields(relationInfoM2M[0].collection_many)"
						:key="field"
						:value="field"
					>
						{{ field }}
					</option>
				</v-simple-select>

				<v-input
					v-if="createM2Mjunction"
					v-model="relationInfoM2M[currentM2MIndex === 0 ? 1 : 0].field_many"
					class="select"
					type="text"
					:placeholder="
						autoM2Msuggestion(
							relationInfoM2M[currentM2MIndex == 0 ? 1 : 0].collection_one,
							'id'
						)
					"
				/>

				<v-checkbox
					v-if="!existing"
					id="createM2Mjunction"
					value="m2mjunction"
					:label="$t('auto_generate')"
					v-model="createM2Mjunction"
				/>

				<v-icon name="arrow_backward" />

				<p class="type-label">{{ $t('related_collection') }}</p>

				<v-simple-select
					v-model="relationInfoM2M[currentM2MIndex == 0 ? 1 : 0].collection_one"
					class="select"
				>
					<optgroup :label="$tc('collection', 2)">
						<option
							v-for="collection in collectionsGrouped.user"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
					<optgroup label="Directus">
						<option
							v-for="collection in collectionsGrouped.system"
							:key="collection"
							:value="collection"
						>
							{{ collection }}
						</option>
					</optgroup>
				</v-simple-select>

				<v-simple-select
					class="select"
					:value="
						primaryKeyFieldByCollection(
							relationInfoM2M[currentM2MIndex === 0 ? 1 : 0].collection_one
						).field
					"
					disabled
				>
					<option
						selected
						:value="
							primaryKeyFieldByCollection(
								relationInfoM2M[currentM2MIndex === 0 ? 1 : 0].collection_one
							).field
						"
					>
						{{
							primaryKeyFieldByCollection(
								relationInfoM2M[currentM2MIndex === 0 ? 1 : 0].collection_one
							).field
						}}
					</option>
				</v-simple-select>
			</form>
		</template>

		<template slot="options">
			<h1 v-if="!existing" class="type-heading-medium">
				{{ $t('field_setup_options') }}
			</h1>

			<label for="__width" class="type-label">{{ $t('field_width') }}</label>
			<v-simple-select v-model="width" name="__width">
				<option value="half">{{ $t('field_width_half') }}</option>
				<option value="half-left">{{ $t('field_width_left') }}</option>
				<option value="half-right">{{ $t('field_width_right') }}</option>
				<option value="full">{{ $t('field_width_full') }}</option>
				<option value="fill">{{ $t('field_width_fill') }}</option>
			</v-simple-select>
			<p class="note type-note">{{ $t('field_width_note') }}</p>

			<form v-if="selectedInterfaceInfo" class="options" @submit.prevent>
				<div
					v-for="(option, optionID) in interfaceOptions.regular"
					:key="optionID"
					class="options"
				>
					<label :for="optionID" class="type-label">{{ option.name }}</label>
					<v-ext-input
						:id="option.interface"
						:name="optionID"
						:type="option.type"
						:length="option.length"
						:readonly="option.readonly"
						:required="option.required"
						:loading="option.loading"
						:options="option.options"
						:value="options[optionID]"
						:fields="selectedInterfaceInfo.options"
						:values="options"
						@input="$set(options, optionID, $event)"
					/>
					<p class="note type-note" v-html="$helpers.snarkdown(option.comment || '')" />
				</div>

				<details
					v-if="Object.keys(interfaceOptions.advanced).length > 0"
					class="advanced"
					:open="existing"
				>
					<summary>{{ $t('advanced_options') }}</summary>
					<div
						v-for="(option, optionID) in interfaceOptions.advanced"
						:key="optionID"
						class="options"
					>
						<label :for="optionID" class="type-label">{{ option.name }}</label>
						<v-ext-input
							:id="option.interface"
							:name="optionID"
							:type="option.type"
							:length="option.length"
							:readonly="option.readonly"
							:required="option.required"
							:loading="option.loading"
							:options="option.options"
							:value="options[optionID] || option.default"
							:fields="selectedInterfaceInfo.options"
							:values="options"
							@input="$set(options, optionID, $event)"
						/>
						<p
							class="note type-note"
							v-html="$helpers.snarkdown(option.comment || '')"
						/>
					</div>
				</details>
			</form>
		</template>
	</v-modal>
</template>

<script>
import formatTitle from '@directus/format-title';
import mapping, { datatypes } from '../type-map';
import { defaultFull } from '../store/modules/permissions/defaults';
import slug from 'slug';
import { cloneDeep, pickBy, find, findIndex, forEach } from 'lodash';

export default {
	name: 'VFieldSetup',
	props: {
		collectionInfo: {
			type: Object,
			required: true
		},
		fieldInfo: {
			type: Object,
			required: true
		},
		saving: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			activeTab: 'interface',
			id: null,
			sort: null,

			field: null,
			isFieldValid: null,
			datatype: null,
			type: null,
			interfaceName: null,
			interfaceFilter: null,
			options: {},
			translation: {},
			readonly: false,
			required: false,
			unique: false,
			note: null,
			hidden_detail: false,
			hidden_browse: false,
			primary_key: false,
			signed: true,
			width: 'full',

			length: null,
			default_value: null,
			validation: null,

			relationInfo: {
				id: null,
				collection_many: null,
				field_many: null,

				collection_one: null,
				field_one: null
			},

			relationInfoM2M: [
				{
					id: null,
					collection_many: null,
					field_many: null,

					collection_one: null,
					field_one: null,

					junction_field: null
				},
				{
					id: null,
					collection_many: null,
					field_many: null,

					collection_one: null,
					field_one: null,

					junction_field: null
				}
			],

			createM2Mjunction: false,
			junctionNameM2M: null
		};
	},
	computed: {
		iconToShow() {
			if (!this.field || this.existing) {
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
		collections() {
			return cloneDeep(this.$store.state.collections);
		},
		collectionsGrouped() {
			let restrictedCollection = [
				'directus_collections',
				'directus_activity',
				'directus_fields',
				'directus_relations'
			];
			const collectionNames = Object.keys(this.collections);
			const system = collectionNames.filter(
				name => name.startsWith('directus_') && !restrictedCollection.includes(name)
			);
			const user = collectionNames.filter(name => !name.startsWith('directus_'));
			return { system, user };
		},
		interfaces() {
			return cloneDeep(this.$store.state.extensions.interfaces);
		},
		interfacesPopular() {
			const groups = [
				{
					title: this.$t('popular'),
					interfaces: [
						'text-input',
						'textarea',
						'wysiwyg',
						'switch',
						'datetime',
						'calendar',
						'file',
						'many-to-one'
					]
				}
			];

			return groups.map(group => ({
				...group,
				interfaces: group.interfaces.map(name => this.interfaces[name])
			}));
		},
		interfacesFiltered() {
			if (!this.interfaceFilter) return this.interfaces;
			return Object.keys(this.interfaces)
				.filter(interfaceName => {
					return formatTitle(interfaceName)
						.toLowerCase()
						.includes(this.interfaceFilter.toLowerCase());
				})
				.map(interfaceName => ({ ...this.interfaces[interfaceName] }));
		},
		databaseVendor() {
			return cloneDeep(this.$store.state.serverInfo.databaseVendor);
		},
		primaryKeyDisabled() {
			if (!this.primaryKeyField) return false;

			return true;
		},
		primaryKeyTooltip() {
			if (!this.primaryKeyField) return null;

			if (this.field === this.primaryKeyField.field) {
				return this.$t('cant_disable_primary');
			}

			return this.$t('max_one_primary_key');
		},
		selectedInterfaceInfo() {
			if (!this.interfaceName) return null;

			return Object.assign({}, this.interfaces[this.interfaceName]);
		},
		interfaceOptions() {
			if (!this.selectedInterfaceInfo) return null;
			const options = Object.assign({}, this.selectedInterfaceInfo.options);
			const regular = pickBy(options, opt => !opt.advanced);
			const advanced = pickBy(options, opt => opt.advanced === true);

			return { regular, advanced };
		},
		existing() {
			return this.id !== null;
		},
		schemaDisabled() {
			return !(this.interfaceName && this.interfaceName.length > 0);
		},
		optionsDisabled() {
			return this.schemaDisabled === true || !this.field;
		},
		displayName() {
			if (!this.field) return '';
			return this.$helpers.formatTitle(this.field);
		},
		availableFieldTypes() {
			if (!this.interfaceName) return [];
			return (
				(this.interfaces[this.interfaceName] &&
					this.interfaces[this.interfaceName].types) ||
				[]
			);
		},
		availableDatatypes() {
			if (!this.type) return [];
			if (this.availableFieldTypes.length === 0) return [];
			return mapping[this.type][this.databaseVendor].datatypes;
		},
		selectedDatatypeInfo() {
			return datatypes[this.databaseVendor][this.datatype];
		},
		fieldTypeDescription() {
			if (!this.type) return null;

			return mapping[this.type] && this.$t(mapping[this.type].description);
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
		relation() {
			if (!this.selectedInterfaceInfo) return null;
			if (!this.selectedInterfaceInfo.relation) return null;

			const relationsThatDontNeedSetup = ['file', 'user'];

			let relation;

			if (typeof this.selectedInterfaceInfo.relation === 'string') {
				relation = this.selectedInterfaceInfo.relation;
			} else {
				relation = this.selectedInterfaceInfo.relation.type;
			}

			if (relationsThatDontNeedSetup.includes(relation)) {
				relation = null;
			}

			return relation;
		},
		buttons() {
			let disabled = false;
			if (this.activeTab === 'interface' && !this.interfaceName) {
				disabled = true;
			}
			if (this.activeTab === 'schema' && !this.field) {
				disabled = true;
			}
			if (this.fieldValid && !this.existing) {
				disabled = true;
			}

			let text = this.$t('next');

			if (
				this.activeTab === 'options' ||
				(this.activeTab === 'schema' && this.hasOptions === false) ||
				this.existing
			) {
				text = this.$t('save');
			}

			return {
				next: {
					disabled,
					text,
					loading: this.saving
				}
			};
		},
		tabs() {
			const tabs = {
				interface: {
					text: this.$tc('interface', 1)
				},
				schema: {
					text: this.$t('schema'),
					disabled: !(this.interfaceName && this.interfaceName.length > 0)
				}
			};

			if (this.relation) {
				tabs.relation = {
					text: this.$t('relation'),
					disabled: this.schemaDisabled === true || !this.field
				};
			}

			tabs.options = {
				text: this.$t('options'),
				disabled: this.schemaDisabled === true || !this.field
			};

			return tabs;
		},
		hasOptions() {
			const interfaceOptions =
				(this.selectedInterfaceInfo && this.selectedInterfaceInfo.options) || {};

			if (this.interfaceName && Object.keys(interfaceOptions).length > 0) return true;

			return false;
		},
		primaryKeyField() {
			return find(this.collectionInfo.fields, {
				primary_key: true
			});
		},
		currentM2MIndex() {
			const index = findIndex(this.relationInfoM2M, info => {
				return info.collection_one === this.collectionInfo.collection;
			});

			if (index === -1) return 0;
			return index;
		},
		isNumeric() {
			return this.type === 'integer';
		}
	},
	watch: {
		fieldInfo() {
			this.useFieldInfo();
		},
		interfaceName(name, oldName) {
			if (!name) return;

			if (name !== this.fieldInfo.interface) {
				const options = {
					...this.interfaceOptions.advanced,
					...this.interfaceOptions.regular
				};

				forEach(options, (info, key) => {
					this.$set(this.options, key, info.default);
				});
			}

			if (this.existing && oldName == null) return;

			this.type = this.availableFieldTypes[0];

			this.datatype = this.type ? mapping[this.type][this.databaseVendor].default : null;

			if (this.existing) return;

			if (this.selectedInterfaceInfo && this.selectedInterfaceInfo.recommended) {
				const {
					defaultValue,
					length,
					validation,
					required
				} = this.selectedInterfaceInfo.recommended;

				if (defaultValue) {
					this.default_value = defaultValue;
				}

				if (length) {
					this.length = length;
				}

				if (validation) {
					this.validation = validation;
				}

				if (required !== undefined) {
					this.required = required;
				}
			}

			this.initRelation();
		},
		type(type) {
			if (this.existing) return;

			if (type) {
				this.datatype = mapping[type][this.databaseVendor].default;
				// NOTE: this is to force string types that are longer than 255 characters into a TEXT mysql
				// type. This should be refactored and cleaned up when this field-setup component is getting
				// refactored.
				// Also, this is hardcoded for MySQL TEXT.
				// Fix for https://github.com/directus/app/issues/1149
				if (this.length > 255 && this.type.toLowerCase() === 'string') {
					this.datatype = 'TEXT';
				}
			}
		},
		datatype() {
			if (this.existing && this.length !== null) return;

			if (
				this.selectedInterfaceInfo &&
				this.selectedInterfaceInfo.recommended &&
				this.selectedInterfaceInfo.recommended.length
			) {
				this.length = this.selectedInterfaceInfo.recommended.length;
				return;
			}

			if (this.selectedDatatypeInfo && this.selectedDatatypeInfo.length) {
				this.length = this.selectedDatatypeInfo.defaultLength;

				if (mapping[this.type][this.databaseVendor].length) {
					this.length = mapping[this.type][this.databaseVendor].length;
				}
			}

			if (this.selectedDatatypeInfo && this.selectedDatatypeInfo.decimal) {
				this.length =
					this.selectedDatatypeInfo.defaultDigits +
					',' +
					this.selectedDatatypeInfo.defaultDecimals;
			}
		},
		lengthDisabled(disabled) {
			if (disabled) {
				this.length = null;
			}
		},
		field(val) {
			this.isFieldValid = !Object.keys(this.collectionInfo.fields).includes(val);

			if (this.relation) {
				if (this.relation === 'm2o') {
					this.relationInfo.field_many = this.field;
				}

				if (this.relation === 'o2m') {
					this.relationInfo.field_one = this.field;
				}
			}
		},
		relationInfoM2M: {
			deep: true,
			handler() {
				if (this.createM2Mjunction) {
					var val0 = (this.relationInfoM2M[0].field_many = this.validateFieldName(
						this.relationInfoM2M[0].field_many
					));
					var val1 = (this.relationInfoM2M[1].field_many = this.validateFieldName(
						this.relationInfoM2M[1].field_many
					));
					this.relationInfoM2M[0].junction_field = val1;
					this.relationInfoM2M[1].junction_field = val0;
				} else {
					this.relationInfoM2M[0].junction_field = this.relationInfoM2M[1].field_many;
					this.relationInfoM2M[1].junction_field = this.relationInfoM2M[0].field_many;
				}
				this.relationInfoM2M[this.currentM2MIndex].field_one = this.field;
			}
		},
		relationInfo: {
			deep: true,
			handler() {
				if (this.relation === 'o2m') {
					this.getM2OID();
				}
			}
		},

		createM2Mjunction(enabled) {
			if (enabled) {
				var ix = this.currentM2MIndex;
				var currentCollection = this.collectionInfo.collection;
				this.relationInfoM2M[ix].field_one = currentCollection;
				this.relationInfoM2M[ix === 0 ? 1 : 0].field_one = currentCollection;
				this.junctionNameM2M = this.autoM2Msuggestion(
					currentCollection,
					this.relationInfoM2M[ix == 0 ? 1 : 0].collection_one
				);
				this.relationInfoM2M[ix].field_many = this.autoM2Msuggestion(
					currentCollection,
					'id'
				);
				this.relationInfoM2M[ix === 0 ? 1 : 0].field_many = this.autoM2Msuggestion(
					this.relationInfoM2M[ix == 0 ? 1 : 0].collection_one,
					'id'
				);
			} else {
				this.initRelation();
			}
		},
		junctionNameM2M(val) {
			if (this.createM2Mjunction) {
				var formatval = this.validateFieldName(val);
				this.junctionNameM2M = formatval;
				this.relationInfoM2M[0].collection_many = formatval;
				this.relationInfoM2M[1].collection_many = formatval;
			} else {
				this.relationInfoM2M[0].collection_many = val;
				this.relationInfoM2M[1].collection_many = val;
			}
		}
	},
	created() {
		this.useFieldInfo();
		this.initRelation();

		this.activeTab = this.existing ? 'options' : 'interface';
	},
	methods: {
		slug,
		interfaceSubtitles(ext) {
			if (ext.types) {
				return this.$helpers.formatTitle(ext.types[0]);
			} else {
				return 'String';
			}
		},
		nextTab() {
			if (this.existing) {
				return this.saveField();
			}

			switch (this.activeTab) {
				case 'interface':
					this.activeTab = 'schema';
					break;
				case 'schema':
					if (this.relation) {
						return (this.activeTab = 'relation');
					}

					if (this.hasOptions === false) {
						return this.saveField();
					}

					this.activeTab = 'options';

					break;
				case 'relation':
					if (this.hasOptions === false) {
						return this.saveField();
					}

					this.activeTab = 'options';
					break;
				case 'options':
				default:
					this.saveField();
					break;
			}
		},
		setInterface(id) {
			this.interfaceName = id;

			if (!this.existing) {
				this.nextTab();
			}
		},
		saveField() {
			const fieldInfo = {
				id: this.id,
				sort: this.sort,
				field: this.field,
				type: this.type,
				datatype: this.datatype,
				interface: this.interfaceName,
				default_value: this.default_value,
				options: this.options,
				readonly: this.readonly,
				required: this.required,
				unique: this.unique,
				note: this.note,
				hidden_detail: this.hidden_detail,
				hidden_browse: this.hidden_browse,
				primary_key: this.primary_key,
				validation: this.validation,
				width: this.width,
				translation: this.translation
			};

			if (this.lengthDisabled === false) {
				fieldInfo.length = this.length;
			}

			if (this.isNumeric === true) {
				fieldInfo.signed = this.signed;
			}

			const result = {
				fieldInfo,
				relation: null
			};

			if (this.relation) {
				if (this.relation === 'm2o') {
					result.relation = { ...this.relationInfo };
					delete result.relation.field_one;
				}

				if (this.relation === 'o2m') {
					result.relation = { ...this.relationInfo };
				}

				if (this.relation === 'm2m') {
					result.relation = [...this.relationInfoM2M];
					if (this.createM2Mjunction === true) {
						this.createM2MjunctionCollection();
					}
				}
			}

			this.$emit('save', result);
		},
		useFieldInfo() {
			if (!this.fieldInfo || Object.keys(this.fieldInfo).length === 0) return;

			// This is somewhat disgusting. The parent fields route shouldn't pass in the
			// stale copy of the field based on the API load, but should instead pass
			// just the name of the field, so we can directly pull it from the store.
			//
			// The parent should also use the store directly, seeing that we are loading
			// the fields nested in the collections anyway (this didn't use to be
			// that way). +1 for future optimizations!
			const fieldName = this.fieldInfo.field;
			const collectionName = this.collectionInfo.collection;
			const storeFieldCopy = cloneDeep(
				this.$store.state.collections[collectionName].fields[fieldName]
			);

			Object.keys(storeFieldCopy).forEach(key => {
				if (storeFieldCopy[key] != null) this[key] = storeFieldCopy[key];
			});

			// 'interface' is a reserved word in JS, so we need to work around that
			this.interfaceName = storeFieldCopy.interface;

			// The API saves the type case insensitive, but the mapping requires case changing
			this.type = this.type && this.type.toLowerCase();
			this.datatype = this.datatype && this.datatype.toUpperCase();
		},
		initRelation() {
			if (!this.relation) return;

			const collection = this.collectionInfo.collection;
			const field = this.field;

			if (this.relation === 'm2o') {
				const existingRelation = cloneDeep(this.$store.getters.m2o(collection, field));

				if (existingRelation) {
					forEach(existingRelation, (val, key) => {
						if (key && val && key.startsWith('collection')) {
							return this.$set(this.relationInfo, key, val.collection);
						}

						if (key && val && key.startsWith('field')) {
							return this.$set(this.relationInfo, key, val.field);
						}

						if (val) {
							this.$set(this.relationInfo, key, val);
						}
					});
				} else {
					this.relationInfo.collection_many = this.collectionInfo.collection;
					this.relationInfo.field_many = this.field;
					this.relationInfo.collection_one = Object.values(
						cloneDeep(this.$store.state.collections)
					)[0].collection;
					this.relationInfo.field_one = find(
						Object.values(cloneDeep(this.$store.state.collections))[0].fields,
						{ primary_key: true }
					).field;
				}
			} else if (this.relation === 'o2m') {
				const existingRelation = cloneDeep(this.$store.getters.o2m(collection, field));

				if (existingRelation) {
					forEach(existingRelation, (val, key) => {
						if (key && val && key.startsWith('collection')) {
							return this.$set(this.relationInfo, key, val.collection);
						}

						if (key && val && key.startsWith('field')) {
							return this.$set(this.relationInfo, key, val.field);
						}

						if (val) {
							this.$set(this.relationInfo, key, val);
						}
					});
				} else {
					this.relationInfo.collection_one = this.collectionInfo.collection;
					this.relationInfo.field_one = this.field;

					this.relationInfo.collection_many = Object.values(
						cloneDeep(this.$store.state.collections)
					)[0].collection;

					this.relationInfo.field_many = find(
						Object.values(cloneDeep(this.$store.state.collections))[0].fields,
						{ primary_key: false }
					).field;

					this.getM2OID();
				}
			} else if (this.relation === 'm2m') {
				const existingRelation = cloneDeep(this.$store.getters.o2m(collection, field));

				if (field && existingRelation) {
					this.junctionNameM2M = existingRelation.collection_many.collection;

					this.relationInfoM2M[0].id = existingRelation.id;

					this.relationInfoM2M[0].field_many = existingRelation.field_many.field;

					this.relationInfoM2M[0].collection_one =
						existingRelation.collection_one.collection;

					this.relationInfoM2M[0].field_one = existingRelation.field_one.field;
					this.relationInfoM2M[0].junction_field =
						existingRelation.junction.field_many.field;

					this.relationInfoM2M[1].id = existingRelation.junction.id;

					this.relationInfoM2M[1].field_many = existingRelation.junction.field_many.field;

					this.relationInfoM2M[1].collection_one =
						existingRelation.junction.collection_one.collection;

					this.relationInfoM2M[1].field_one =
						existingRelation.junction.field_one &&
						existingRelation.junction.field_one.field;

					this.relationInfoM2M[1].junction_field = existingRelation.field_many.field;
				} else {
					this.junctionNameM2M = Object.keys(this.collections)[0];

					this.relationInfoM2M[0].field_many = Object.values(
						Object.values(this.collections)[0].fields
					)[0].field;

					this.relationInfoM2M[0].collection_one = this.collectionInfo.collection;

					this.relationInfoM2M[0].junction_field = Object.values(
						Object.values(this.collections)[0].fields
					)[0].field;

					this.relationInfoM2M[1].field_many = Object.values(
						Object.values(this.collections)[0].fields
					)[0].field;

					this.relationInfoM2M[1].collection_one = Object.keys(this.collections)[1];

					this.relationInfoM2M[1].junction_field = Object.values(
						Object.values(this.collections)[0].fields
					)[0].field;

					// Recommended relationships
					// Interfaces can recommend a related collection. For example, the files
					// interface will recommend the collection_many to be directus_files
					// in order to make it easier for the user to setup the interface
					if (
						this.selectedInterfaceInfo.relation &&
						typeof this.selectedInterfaceInfo.relation === 'object'
					) {
						if (this.selectedInterfaceInfo.relation.relatedCollection) {
							this.relationInfoM2M[1].collection_one = this.selectedInterfaceInfo.relation.relatedCollection;
						}
					}
				}
			}
		},
		getM2OID() {
			const collection = this.relationInfo.collection_many;
			const field = this.relationInfo.field_many;

			const m2o = cloneDeep(this.$store.getters.m2o(collection, field));

			if (m2o) {
				this.relationInfo.id = m2o.id;
			} else {
				this.relationInfo.id = null;
			}
		},
		fields(collection) {
			if (!collection) return {};
			return this.collections[collection].fields;
		},
		fieldsNonSystem(collection) {
			if (!collection) return {};
			var fields = this.collections[collection].fields;
			for (var key in fields) {
				if (fields.hasOwnProperty(key) && fields[key].primary_key) {
					delete fields[key];
				}
			}
			return this.collections[collection].fields;
		},
		primaryKeyFieldByCollection(collection) {
			const fields = this.fields(collection);
			return find(fields, { primary_key: true });
		},
		validateFieldName(string) {
			// Based on https://gist.github.com/mathewbyrne/1280286
			return string
				.toString()
				.replace(/\s+/g, '_') // Replace spaces with _
				.replace(/[^\w_]+/g, '') // Remove all non-word chars
				.replace(/__+/g, '_') // Replace multiple _ with single _
				.toLowerCase();
		},
		autoM2Msuggestion(collectionName, suffix) {
			return collectionName + '_' + suffix;
		},

		// issue here getting the new field to add M2M relationships without refreshing
		createM2MjunctionCollection() {
			var collectionData = {
				collection: this.junctionNameM2M,
				hidden: true,
				note: this.$t('junction_collection'),
				fields: [
					{
						field: 'id',
						type: 'integer',
						datatype: 'int',
						interface: 'primary-key',
						primary_key: true,
						auto_increment: true,
						signed: false,
						length: 10
					},
					{
						field: this.relationInfoM2M[0].field_many,
						type: this.primaryKeyFieldByCollection(
							this.relationInfoM2M[0].collection_one
						).type,
						length: 10,
						datatype: this.primaryKeyFieldByCollection(
							this.relationInfoM2M[0].collection_one
						).datatype,
						interface: null,
						readonly: false,
						required: true
					},
					{
						field: this.relationInfoM2M[1].field_many,
						type: this.primaryKeyFieldByCollection(
							this.relationInfoM2M[1].collection_one
						).type,
						length: 10,
						datatype: this.primaryKeyFieldByCollection(
							this.relationInfoM2M[1].collection_one
						).datatype,
						interface: null,
						readonly: false,
						required: true
					}
				]
			};
			var fieldDispatch = {
				id: {
					auto_increment: true,
					collection: this.junctionNameM2M,
					datatype: 'int',
					default_value: null,
					field: 'id',
					group: null,
					hidden_detail: true,
					hidden_browse: true,
					interface: 'primary-key',
					length: '10',
					locked: 0,
					note: null,
					options: null,
					primary_key: true,
					readonly: 0,
					required: false,
					signed: false,
					sort: 1,
					translation: null,
					type: 'integer',
					unique: false,
					validation: null,
					width: 4
				}
			};
			fieldDispatch[this.relationInfoM2M[0].field_many] = {
				collection: this.junctionNameM2M,
				field: this.relationInfoM2M[0].field_many,
				datatype: this.primaryKeyFieldByCollection(this.relationInfoM2M[0].collection_one)
					.datatype,
				unique: false,
				primary_key: false,
				auto_increment: false,
				default_value: null,
				note: null,
				signed: true,
				type: this.primaryKeyFieldByCollection(this.relationInfoM2M[0].collection_one).type,
				sort: 0,
				interface: null,
				hidden_detail: true,
				hidden_browse: true,
				required: true,
				options: null,
				locked: false,
				translation: null,
				readonly: false,
				width: 4,
				validation: null,
				group: null,
				length: 10
			};
			fieldDispatch[this.relationInfoM2M[1].field_many] = {
				collection: this.junctionNameM2M,
				field: this.relationInfoM2M[1].field_many,
				datatype: this.primaryKeyFieldByCollection(this.relationInfoM2M[1].collection_one)
					.datatype,
				unique: false,
				primary_key: false,
				auto_increment: false,
				default_value: null,
				note: null,
				signed: true,
				type: this.primaryKeyFieldByCollection(this.relationInfoM2M[1].collection_one).type,
				sort: 0,
				interface: null,
				hidden_detail: true,
				hidden_browse: true,
				required: true,
				options: null,
				locked: false,
				translation: null,
				readonly: false,
				width: 4,
				validation: null,
				group: null,
				length: 10
			};
			this.$api
				.createCollection(collectionData, {
					fields: '*.*'
				})
				.then(res => res.data)
				.then(collection => {
					this.$store.dispatch('addCollection', {
						...collection,
						fields: fieldDispatch
					});
					this.$store.dispatch('addPermission', {
						collection: this.junctionNameM2M,
						permission: {
							$create: defaultFull,
							...defaultFull
						}
					});
				})
				.catch(error => {
					this.$events.emit('error', {
						notify: this.$t('something_went_wrong_body'),
						error
					});
				});
		}
	}
};
</script>

<style lang="scss" scoped>
.type-heading-medium {
	// max-width: 80%;
	margin-bottom: 30px;
}

.currently-selected {
	margin-bottom: 40px;
}

.interface-filter {
	margin-bottom: 40px;
}

.type-note {
	margin-top: var(--input-note-margin);
	& b {
		font-weight: var(--weight-bold);
	}
}

.note {
	display: block;
	margin-top: var(--input-note-margin);
}

.interfaces {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	grid-gap: 20px;

	article {
		display: block;
		flex-basis: 160px;
		flex-shrink: 0;
		overflow: hidden;
		transition: box-shadow var(--fast) var(--transition-out);
		cursor: pointer;

		.header {
			background-color: var(--card-background-color);
			border-radius: var(--border-radius);
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 20px 0;
			transition: background-color var(--fast) var(--transition-out);
			i {
				color: var(--card-text-color-disabled) !important;
			}
		}

		&.active {
			.header {
				background-color: var(--card-background-color-hover);
				color: var(--card-text-color);
				transition: background-color var(--fast) var(--transition-in);
				i {
					color: var(--card-text-color) !important;
				}
			}
		}

		&:hover {
			.header {
				background-color: var(--card-background-color-hover);
				i {
					color: var(--card-text-color) !important;
				}
			}
		}

		.body {
			padding-top: 8px;
		}

		h2 {
			margin: 0;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		p {
			color: var(--note-text-color);
			font-size: 14px;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}
}

form.schema {
	.type-label {
		margin-bottom: var(--input-label-margin);
		display: inline-block;
	}

	.name {
		margin-bottom: 30px;
	}

	.name-input {
		font-family: 'Roboto Mono', monospace;
	}

	.advanced-form,
	.toggles,
	.name {
		display: grid;
		grid-gap: 32px 32px;
		grid-template-columns: 1fr 1fr;

		.type-note {
			display: inline-block;
			margin-top: var(--input-note-margin);
		}

		.toggle {
			display: flex;
			align-items: center;
			text-transform: capitalize;
			font-size: 1rem;
			cursor: pointer;
			width: max-content;

			> *:first-child {
				margin-right: 10px;
			}
		}

		.translation {
			grid-column: 1 / span 2;
		}
	}
}

form.options {
	margin-top: 30px;
	label {
		margin-bottom: var(--input-label-margin);
	}
	div.options {
		margin-bottom: 30px;
		&:last-of-type {
			margin-bottom: 20px;
		}
	}
}

details {
	position: relative;
	margin-top: 60px;
	border-top: 2px solid var(--input-border-color);
	padding-top: 40px;
	summary {
		font-size: 18px;
		color: var(--note-text-color);
		font-weight: 400;
		transition: var(--fast) var(--transition);
		margin-top: -16px;
		background-color: var(--page-background-color);
		display: inline-block;
		position: absolute;
		top: 4px;
		cursor: pointer;

		&:hover {
			color: var(--page-text-color);
		}

		&::-webkit-details-marker {
			display: none;
		}

		&::after {
			content: 'unfold_more';
			direction: ltr;
			display: inline-block;
			font-family: 'Material Icons';
			font-size: 18px;
			color: var(--input-icon-color);
			font-style: normal;
			font-weight: normal;
			letter-spacing: normal;
			line-height: 1;
			text-transform: none;
			white-space: nowrap;
			word-wrap: normal;
			-webkit-font-feature-settings: 'liga';
			-webkit-font-smoothing: antialiased;
			transition: var(--fast) var(--transition);
			width: 28px;
			height: 24px;
			margin-left: 6px;
			margin-top: 2px;
			float: right;
		}
	}

	&[open] summary::after {
		content: 'unfold_less';
	}
}

.no-results {
	margin: 20px auto;
	min-height: 0;
}

.required {
	color: var(--input-required-color);
	vertical-align: super;
}

.single {
	display: grid;
	grid-template-areas:
		'a _ b'
		'c _ d'
		'e f g';
	grid-template-columns: 1fr 20px 1fr;
	grid-gap: 10px 0;
	justify-content: center;
	align-items: center;

	span:first-of-type {
		grid-area: a;
	}

	span:last-of-type {
		grid-area: b;
	}

	.select {
		&:first-of-type {
			grid-area: c;
		}

		&:nth-of-type(2) {
			grid-area: e;
		}

		&:nth-of-type(3) {
			grid-area: d;
		}

		&:nth-of-type(4) {
			grid-area: g;
		}
	}

	.v-icon {
		grid-area: f;
		color: var(--input-border-color);
		transform: translateX(-2px);
	}
}

.full {
	margin-top: 40px;
	display: grid;
	grid-template-areas:
		'a b c d e'
		'f g h i j'
		'k l m n o'
		'p q r s t'
		'u u v w w';
	grid-template-columns: 1fr 20px 1fr 20px 1fr;
	grid-gap: 10px 0;
	justify-content: center;
	align-items: center;

	p:first-of-type {
		grid-area: a;
	}

	p:nth-of-type(2) {
		grid-area: c;
	}

	p:last-of-type {
		grid-area: e;
	}

	.select {
		&:first-of-type {
			grid-area: f;
		}

		&:nth-of-type(2) {
			grid-area: k;
		}

		&:nth-of-type(3) {
			grid-area: h;
		}

		&:nth-of-type(4) {
			grid-area: m;
		}

		&:nth-of-type(5) {
			grid-area: r;
		}

		&:nth-of-type(6) {
			grid-area: j;
		}

		&:nth-of-type(7) {
			grid-area: t;
		}
	}

	.v-icon {
		grid-area: l;
		color: var(--input-border-color);
		transform: translateX(-2px);

		&:last-of-type {
			grid-area: s;
		}
	}

	.v-checkbox {
		&:first-of-type {
			grid-area: v;
		}
	}
}

.toggles {
	margin-top: 32px;
}

label {
	margin-bottom: var(--input-label-margin);
}

hr {
	margin: 32px 0;
	border: 0;
	border-top: 2px solid var(--blue-grey-50);
}
</style>
