<template>
	<div class="v-simple-select">
		<select ref="selectElement" :value="value" :disabled="disabled" @change="stageValue">
			<option disabled :selected="value == null" value="">
				{{ placeholder || '--' }}
			</option>
			<slot />
		</select>
		<div class="preview">
			<template v-if="value">
				{{ valueText }}
			</template>
			<span v-else class="placeholder">{{ placeholder || '--' }}</span>
			<v-icon class="icon" name="arrow_drop_down" />
		</div>
	</div>
</template>

<script>
import { isEqual } from 'lodash';

export default {
	name: 'VSimpleSelect',
	props: {
		value: {
			type: String,
			default: null
		},
		placeholder: {
			type: String,
			default: null
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			valueNames: {}
		};
	},
	computed: {
		valueText() {
			return this.valueNames[this.value];
		}
	},
	watch: {
		value() {
			this.getValueNames();
		}
	},
	mounted() {
		this.getValueNames();
	},
	updated() {
		this.getValueNames();
	},
	methods: {
		stageValue(event) {
			this.$emit('input', event.target.value);
		},
		getValueNames() {
			const selectElement = this.$refs.selectElement;
			const valueNames = {};
			const children = Array.from(selectElement.querySelectorAll('option'));

			children.forEach(element => {
				valueNames[element.value.trim()] = element.innerText.trim();
			});

			if (!isEqual(valueNames, this.valueNames)) {
				this.valueNames = valueNames;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.v-simple-select {
	position: relative;

	.preview {
		height: var(--input-height);
		border: var(--input-border-width) solid var(--input-border-color);
		border-radius: var(--border-radius);
		background-color: var(--white);
		display: flex;
		align-items: center;
		padding-left: 10px;
		color: var(--input-text-color);
		background-color: var(--input-background-color);
		font-size: var(--input-font-size);
		text-transform: none;

		.icon {
			position: absolute;
			right: 10px;
			top: 50%;
			transform: translateY(-50%);
			user-select: none;
			pointer-events: none;
			color: var(--input-icon-color);
		}
	}

	select {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
		cursor: pointer;
		appearance: none;
	}

	select:hover + .preview {
		border-color: var(--input-border-color-hover);
	}

	select:focus + .preview {
		border-color: var(--input-border-color-focus);
	}

	select[disabled] {
		cursor: not-allowed;

		& + .preview {
			background-color: var(--input-background-color-disabled);
		}
	}
}
</style>
