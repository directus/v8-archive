<template>
	<div class="interface-toggle-icon">
		<input
			:id="randomID"
			type="checkbox"
			:disabled="readonly"
			@change="updateValue($event.target.checked)"
		/>
		<label :for="randomID" :style="{ color: `var(--${colorChange})` }">
			<v-icon :name="icon" />
			<span>{{ textChange }}</span>
		</label>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';
import shortid from 'shortid';

export default {
	name: 'InterfaceToggleIcon',
	mixins: [mixin],
	computed: {
		icon() {
			return this.value ? this.options.iconActive : this.options.iconInactive;
		},
		colorChange() {
			return this.value ? this.options.colorActive : this.options.colorInactive;
		},
		textChange() {
			return this.value ? this.options.textActive : this.options.textInactive;
		},
		randomID() {
			return shortid.generate();
		}
	},
	methods: {
		updateValue(value) {
			this.$emit('input', value);
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-toggle-icon {
	user-select: none;
}

input {
	position: absolute !important;
	height: 1px;
	width: 1px;
	overflow: hidden;
	clip: rect(1px, 1px, 1px, 1px);
	clip-path: polygon(0px 0px, 0px 0px, 0px 0px, 0px 0px);
}

input[disabled] + label {
	opacity: 0.6;
}

label {
	cursor: pointer;
	display: inline-block;
	span {
		margin-left: 8px;
		font-size: var(--input-font-size);
	}
}
</style>
