<template>
	<div>
		<v-slider
			:key="`${key}-0`"
			:value="values[0]"
			:min="0"
			:max="this.format === 'rgb' ? 255 : 359"
			:unit="this.format === 'rgb' ? '(R)' : '(H)'"
			@input="emitValue(0, $event)"
		/>
		<v-slider
			:key="`${key}-1`"
			:value="values[1]"
			:min="0"
			:max="this.format === 'rgb' ? 255 : 100"
			:unit="this.format === 'rgb' ? '(G)' : '(S)'"
			@input="emitValue(1, $event)"
		/>
		<v-slider
			:key="`${key}-2`"
			:value="values[2]"
			:min="0"
			:max="this.format === 'rgb' ? 255 : 100"
			:unit="this.format === 'rgb' ? '(B)' : '(L)'"
			@input="emitValue(2, $event)"
		/>
	</div>
</template>

<script>
import Color from 'color';
import shortid from 'shortid';
import { clone } from 'lodash';

export default {
	props: {
		value: {
			type: String,
			default: null
		},
		format: {
			type: String,
			required: true,
			default: 'rbg'
		}
	},
	computed: {
		values() {
			return new Color(this.value)
				[this.format]()
				.array()
				.map(v => Math.round(v));
		},
		key() {
			return shortid.generate();
		}
	},
	methods: {
		emitValue(index, $event) {
			const values = clone(this.values);
			values[index] = $event;
			this.$emit('input', new Color[this.format](values).hex());
		}
	}
};
</script>

<style lang="scss" scoped>
.v-slider {
	margin-top: 8px;
}
</style>
