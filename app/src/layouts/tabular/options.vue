<template>
	<form @submit.prevent>
		<label class="type-label">{{ $t('layouts.tabular.fields') }}</label>
		<draggable v-model="sortList" direction="vertical" @end="sort">
			<div
				v-for="field in sortList"
				:key="'tabular-layout-options-field-' + field.field"
				class="draggable"
			>
				<v-checkbox
					:id="'tabular-layout-options-field-' + field.field"
					:key="field.field"
					class="checkbox"
					:label="field.name"
					:value="field.field"
					:inputValue="fieldsInUse.includes(field.field)"
					@change="toggleField(field.field)"
				></v-checkbox>
				<v-icon class="handle" name="drag_handle" color="--input-border-color" />
			</div>
		</draggable>
		<label for="spacing" class="type-label">{{ $t('spacing') }}</label>
		<v-select
			id="spacing"
			:value="viewOptions.spacing || 'comfortable'"
			:options="{
				compact: $t('compact'),
				cozy: $t('cozy'),
				comfortable: $t('comfortable')
			}"
			class="select"
			icon="reorder"
			@input="setSpacing"
		></v-select>
	</form>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/layout';

export default {
	mixins: [mixin],
	data() {
		return {
			sortList: null
		};
	},
	computed: {
		fieldsInUse() {
			if (!this.viewQuery || !this.viewQuery.fields)
				return Object.values(this.fields)
					.filter(field => field.primary_key === false || field.primary_key === '0')
					.filter(field => field.hidden_browse !== true)
					.slice(0, 4)
					.map(field => field.field);

			if (this.viewQuery.fields === '') return [];

			return this.viewQuery.fields.split(',').filter(field => this.fields[field]);
		}
	},
	watch: {
		fields() {
			this.initSortList();
		}
	},
	created() {
		this.initSortList();
	},
	methods: {
		setSpacing(value) {
			this.$emit('options', {
				spacing: value
			});
		},
		toggleField(fieldID) {
			const fieldsInUse = [...this.fieldsInUse];

			if (fieldsInUse.includes(fieldID)) {
				fieldsInUse.splice(fieldsInUse.indexOf(fieldID), 1);
			} else {
				fieldsInUse.push(fieldID);
			}

			const fields = this.sortList
				.map(fieldInfo => fieldInfo.field)
				.filter(fieldID => fieldsInUse.includes(fieldID))
				.join();

			this.$emit('query', {
				fields
			});
		},
		sort() {
			this.$emit('query', {
				...this.viewQuery,
				fields: this.sortList
					.map(obj => obj.field)
					.filter(fieldID => this.fieldsInUse.includes(fieldID))
					.join()
			});
		},
		initSortList() {
			this.sortList = [
				...this.fieldsInUse.map(fieldID => this.fields[fieldID]),
				...Object.values(this.fields).filter(
					fieldInfo => !this.fieldsInUse.includes(fieldInfo.field)
				)
			];
		}
	}
};
</script>

<style lang="scss" scoped>
fieldset {
	padding: 8px 0 0 0;
}

.type-label {
	margin-top: var(--form-vertical-gap);
	margin-bottom: var(--input-label-margin);
	&:first-of-type {
		margin-top: 0;
	}
}

.draggable {
	display: flex;
	align-items: center;
	justify-content: space-between;
	cursor: ns-resize;
	padding: 2px 0;

	&:hover {
		i {
			color: var(--input-border-color-hover);
		}
	}
}
</style>
