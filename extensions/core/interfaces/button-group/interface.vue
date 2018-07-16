<template>
	<div 
		class="interface-button-group">
		<label 
			v-for="(item,index) in options.choices" 
			:key="'button-group-item'+index"
			class="button-group-item">
			<input 
				type="radio"
				:name="name"
				:disabled="readonly"
				@change="$emit('input', item.value)"
				:value="item.value">
			<span class="button-group-button">
				<i v-if="item.icon" class="material-icons">{{item.icon}}</i>
				<span v-if="item.label">{{item.label}}</span>
			</span>
		</label>
	</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
	name: "interface-button-group",
	mixins: [mixin],
	computed: {},
	methods: {}
};
</script>

<style lang="scss" scoped>
.interface-button-group {
	display: inline-flex;
	flex-wrap: wrap;
}

.button-group-item {
	input[type="radio"] {
		height: 0;
		position: absolute;
		/**
			Focused State
		*/
		&:focus {
			+ .button-group-button {
				//background-color: var(--lighter-gray);
				background-color: var(--light-blue-50);
			}
		}
		/**
			Checked State
		*/
		&:checked {
			+ .button-group-button {
				background-color: var(--accent);
				color: var(--white);
			}
		}
		/**
			Disabled State
		*/
		&:disabled {
			+ .button-group-button {
				border-color: var(--lighter-gray);
				background-color: var(--lightest-gray);
				color: var(--gray);
				cursor: not-allowed;
			}
			&:checked {
				+ .button-group-button {
					background-color: var(--lighter-gray);
					color: var(--gray);
				}
			}
		}
	}
	+ .button-group-item {
		.button-group-button {
			margin-left: calc(-1 * var(--input-border-width));
		}
	}
	&:first-child {
		.button-group-button {
			border-radius: var(--border-radius) 0 0 var(--border-radius);
		}
	}
	&:last-child {
		.button-group-button {
			border-radius: 0 var(--border-radius) var(--border-radius) 0;
		}
	}
}

.button-group-button {
	border: var(--input-border-width) solid var(--action);
	cursor: pointer;
	transition: var(--fast) var(--transition);
	transition-property: border-color, background-color, color;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 0px 20px;
	height: 40px;
	line-height: 40px;
	white-space: nowrap;
	color: var(--action-dark);
	&:hover {
		background-color: var(--action);
		color: var(--white);
	}
	i {
		font-size: 18px;
		+ span {
			margin-left: 4px;
		}
	}
}

@media only screen and (max-width: 800px) {
	.interface-button-group {
		flex-direction: column;
		display: inline-flex;
	}
	.button-group-item {
		+ .button-group-item {
			.button-group-button {
				margin-left: 0;
				margin-top: calc(-1 * var(--input-border-width));
			}
		}
		&:first-child {
			.button-group-button {
				border-radius: var(--border-radius) var(--border-radius) 0 0;
			}
		}
		&:last-child {
			.button-group-button {
				border-radius: 0 0 var(--border-radius) var(--border-radius);
			}
		}
	}
}
</style>
