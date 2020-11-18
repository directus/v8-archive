<template>
	<details :open="open" :class="type" class="v-details">
		<summary>{{ title }}</summary>
		<div class="content"><slot /></div>
	</details>
</template>

<script>
export default {
	name: 'VDetails',
	props: {
		title: {
			type: String,
			default: null
		},
		open: {
			type: [Boolean, Number],
			default: false
		},
		type: {
			type: String,
			default: 'group',
			validator(val) {
				return ['group', 'break'].includes(val);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.v-details {
	position: relative;
	width: 100%;
	margin-top: 10px;
	border-top: 2px solid var(--input-border-color);
	padding-top: 40px;
	margin-bottom: 80px;

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

		&::-webkit-details-marker {
			display: none;
		}

		&:hover,
		.user-is-tabbing &:focus {
			color: var(--page-text-color);
			&::after {
				color: var(--page-text-color);
			}
		}
	}

	&.group {
		padding: 0px;
		border: none;
		border-top: 2px solid var(--input-border-color);

		// &[open] {
		//   padding: 20px 0;
		// }

		.content {
			padding-top: 32px;
		}

		summary {
			border: 0;
			margin-bottom: 0;
		}
	}

	&[open] {
		summary::after {
			content: 'unfold_less';
		}
	}
}
</style>
