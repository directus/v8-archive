<template>
	<v-sheet v-if="relationshipSetup">
		<v-select
			v-model="currentLanguage"
			class="language-picker"
			:options="options.languages"
			icon="translate"
		/>

		<hr />

		<div v-if="loading === false && initialValues !== null" class="body">
			<v-form
				full-width
				:key="currentLanguage"
				:collection="relation.collection_many.collection"
				:fields="translatedFields"
				:values="currentLanguageValues"
				:primary-key="existing && existing[translationsCollectionPrimaryKeyField]"
				@stage-value="saveLanguage"
			/>
		</div>

		<v-spinner v-else />
	</v-sheet>

	<v-notice v-else color="warning" icon="warning">
		{{ $t('relationship_not_setup') }}
	</v-notice>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import { mapValues, clone, find, merge } from 'lodash';

export default {
	mixins: [mixin],
	data() {
		return {
			currentLanguage: Object.keys(this.options.languages)[0],
			loading: false,
			initialValues: null,
			relationalChanges: []
		};
	},
	computed: {
		translatedFields() {
			if (this.relationshipSetup === false) {
				return;
			}

			return mapValues(this.relation.collection_many.fields, field => {
				field = clone(field); // remove vue reactivity

				// Prevent updating the recursive relational key
				if (field.field === this.relation.field_many.field) {
					field.readonly = true;
				}

				return field;
			});
		},
		defaults() {
			return mapValues(clone(this.translatedFields), f => f.default_value);
		},
		existing() {
			return find(this.initialValues, {
				[this.options.languageField]: this.currentLanguage
			});
		},
		translationsCollectionPrimaryKeyField() {
			return Object.values(this.relation.collection_many.fields).find(
				field => field.primary_key === true
			).field;
		},
		currentLanguageValues() {
			const existingChanges = find(this.relationalChanges, {
				[this.options.languageField]: this.currentLanguage
			});

			return merge({}, this.existing || this.defaults, existingChanges);
		},
		relationshipSetup() {
			return !!this.relation?.collection_many;
		},
		currentPrimaryKey() {
			const { field } = find(this.fields, { primary_key: true });
			return this.values[field];
		}
	},
	watch: {
		relationalChanges: {
			deep: true,
			handler(value) {
				if (value) {
					this.emitValue(value);
				}
			}
		}
	},
	created() {
		this.fetchInitial();
	},
	methods: {
		saveLanguage({ field, value }) {
			const existingChanges = find(this.relationalChanges, {
				[this.options.languageField]: this.currentLanguage
			});

			if (existingChanges) {
				this.relationalChanges = this.relationalChanges.map(update => {
					if (update[this.options.languageField] === this.currentLanguage) {
						return merge({}, update, { [field]: value });
					}

					return update;
				});
			} else {
				const update = {
					[field]: value,
					[this.options.languageField]: this.currentLanguage
				};

				if (this.existing) {
					const primaryKeyField = find(this.translatedFields, { primary_key: true })
						.field;
					const relatedPrimaryKey = this.existing[primaryKeyField];
					update[primaryKeyField] = relatedPrimaryKey;
				}

				this.relationalChanges = [...this.relationalChanges, update];
			}
		},
		emitValue(value) {
			if (this.initialValues.length == 0) {
				this.$emit('input', value);
				return;
			}

			// This is the key in the nested related object that holds the parent item again
			const languageField = this.options.languageField;

			const newValue = this.initialValues
				.map(before => {
					const languageKey = before[languageField];

					// Check if the current item was saved before
					const after = value.find(i => i[languageField] === languageKey);

					if (after) {
						return merge({}, before, after);
					}
					return before;
				})
				.filter(i => i);

			// if there is new langauge added, then need to push to the results
			value.forEach(after => {
				const languageKey = after[languageField];
				const before = newValue.find(i => i[languageField] === languageKey);
				if (!before) {
					newValue.push(after);
				}
			});

			this.$emit('input', newValue);
		},
		async fetchInitial() {
			if (this.relationshipSetup === false) {
				return;
			}

			/*if (this.newItem) {
				this.initialValues = [];
				return;
			}*/

			if (this.values && this.values[this.relation.field_one.field]) {
				this.initialValues = this.values[this.relation.field_one.field];
				return;
			}

			this.loading = true;
			const { collection } = this.relation.collection_many;
			const { field } = this.relation.field_many;

			const { data } = await this.$api.getItems(collection, {
				filter: {
					[field]: {
						eq: this.currentPrimaryKey
					}
				}
			});

			this.initialValues = data;
			this.loading = false;
		}
	}
};
</script>

<style lang="scss" scoped>
.language-picker {
	margin-bottom: 24px;
}

hr {
	border: none;
	border-bottom: 2px solid var(--input-border-color);
	border-radius: 1px;
	margin-bottom: 24px;
}

.body {
	--form-vertical-gap: 24px;
	--form-horizontal-gap: 12px;
	--type-label-size: 15px;
	--input-height: 44px;
	--input-font-size: 14px;
	--input-label-margin: 4px;
	--input-background-color-alt: var(--input-background-color);
}
</style>
