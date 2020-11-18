<template>
	<v-sheet ref="interfaceRoot" class="repeater-row">
		<div class="header">
			<div class="drag-handle">
				<v-icon name="drag_handle" />
			</div>
			<div class="content" :class="{ 'two-up': inline && fields.length === 2 }">
				<template v-if="inline">
					<v-ext-input
						v-for="field in fields"
						:id="field.interface || 'text-input'"
						:key="field.field"
						:name="field.field"
						:required="field.required"
						:readonly="field.readonly"
						:options="field.options"
						:type="field.type"
						:datatype="field.datatype"
						:value="row[field.field]"
						:fields="fields"
						:values="row"
						:length="field.length"
						:width="field.width"
						@input="
							$emit('input', {
								field: field.field,
								value: $event
							})
						"
					/>
				</template>
				<button
					v-else
					type="button"
					:class="showPlaceholder ? 'placeholder' : 'preview'"
					@click="$emit('open')"
				>
					{{ showPlaceholder ? placeholder : displayValue }}
				</button>
			</div>

			<v-contextual-menu
				v-if="duplicable"
				class="more-options"
				placement="bottom-end"
				:options="rowOptions"
				@click="rowOptionsClicked"
			></v-contextual-menu>
			<button v-else type="button" @click="$emit('remove')">
				<v-icon name="delete_outline" class="remove" />
			</button>
		</div>
		<div v-if="inline === false" v-show="open" class="body">
			<v-form
				full-width
				:fields="fields"
				:values="row"
				:new-item="newItem"
				@stage-value="$emit('input', $event)"
			/>
		</div>
	</v-sheet>
</template>

<script>
import getFieldsFromTemplate from '@/helpers/get-fields-from-template';

export default {
	name: 'RepeaterRow',
	props: {
		row: {
			type: Object,
			required: true
		},
		fields: {
			type: Array,
			required: true
		},
		inline: {
			type: Boolean,
			default: false
		},
		template: {
			type: String,
			default: null
		},
		duplicable: {
			type: Boolean,
			default: false
		},
		open: {
			type: Boolean,
			default: false
		},
		placeholder: {
			type: String,
			required: true
		}
	},
	computed: {
		newItem() {
			if (this.row.newItem === true) return true;
			return false;
		},
		displayValue() {
			if (!this.template) {
				return this.row[this.fields[0].field];
			}

			let preview = this.$helpers.micromustache.render(this.template, this.row);

			return preview;
		},
		showPlaceholder() {
			let fields = [this.fields[0].field];

			if (this.template) {
				fields = getFieldsFromTemplate(this.template);
			}

			const fieldsHaveValue = fields.every(field => {
				return this.row[field] !== null && this.row[field]?.length > 0;
			});

			return fieldsHaveValue === false;
		},
		rowOptions() {
			return [
				{
					text: this.$t('delete'),
					icon: 'delete_outline'
				},
				{
					text: this.$t('duplicate'),
					icon: 'control_point_duplicate'
				}
			];
		}
	},
	methods: {
		rowOptionsClicked(option) {
			switch (option) {
				case 0:
					this.$emit('remove');
					break;
				case 1:
					this.$emit('duplicate');
					break;
				default:
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.repeater-row {
	margin-bottom: 8px;
	padding: 0;
}

.header {
	--form-vertical-gap: 24px;
	--form-horizontal-gap: 12px;
	--type-label-size: 15px;
	--input-height: 44px;
	--input-font-size: 14px;
	--input-label-margin: 4px;
	--input-background-color-alt: var(--input-background-color);

	display: flex;
	align-items: center;
	min-height: var(--input-height);
	padding: var(--input-padding);

	.drag-handle {
		color: var(--input-icon-color);
		cursor: grab;
	}

	.content {
		flex-grow: 1;
		display: grid;
		grid-template-columns: repeat(1, 1fr);
		grid-gap: 8px;
		padding: 0 8px;

		&.two-up {
			grid-template-columns: repeat(2, 1fr);
		}

		.placeholder {
			color: var(--input-placeholder-color);
			font-style: italic;
		}

		.preview,
		.placeholder {
			text-align: left;
			min-height: 32px;
		}
	}

	.remove {
		transition: color var(--fast) var(--transition);
		color: var(--input-icon-color);
		&:hover {
			color: var(--danger);
		}
	}
}

.body {
	--form-vertical-gap: 24px;
	--form-horizontal-gap: 12px;
	--type-label-size: 15px;
	--input-height: 44px;
	--input-font-size: 14px;
	--input-label-margin: 4px;
	--input-background-color-alt: var(--input-background-color);
	padding: 8px;
}
</style>
