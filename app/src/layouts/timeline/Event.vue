<template>
	<div class="event">
		<div class="line" :class="{ connect: connect }">
			<div
				class="point"
				:style="{ backgroundColor: 'var(--' + (data.color || 'input-border-color') + ')' }"
			></div>
		</div>
		<div class="container" @click="$router.push(data.to)">
			<div class="title">
				{{ data.title }}
			</div>
			<div class="content">
				<v-ext-display
					v-if="data.contentType"
					:id="data.contentType.name"
					class="display"
					:name="data.contentType.name"
					:type="data.contentType.type"
					:value="data.content"
					:interface-type="data.contentType.interface"
					:options="data.contentType.options"
					@click.native.stop=""
				/>
				<div class="time">{{ time }}</div>
			</div>
		</div>
	</div>
</template>

<script>
import formatDistance from 'date-fns/formatDistance';

export default {
	components: {},
	props: {
		data: {
			type: Object,
			default: null
		},
		connect: {
			type: Boolean,
			default: true
		}
	},
	computed: {
		time() {
			return formatDistance(new Date(), this.data.time);
		}
	}
};
</script>

<style lang="scss" scoped>
.event {
	position: relative;

	&:first-child {
		.container {
			border-top: none;
		}
		.line .point {
			top: 6px;
		}
	}

	.line {
		position: absolute;
		top: 17px;
		left: 36px;
		height: 102%;
		width: 2px;
		transform: translate(-50%, 0);

		&.connect {
			background-color: var(--sidebar-background-color);
		}

		.point {
			width: 12px;
			height: 12px;
			border-radius: 50%;
			margin-left: 50%;
			transform: translate(-50%, -50%);
			border: var(--page-background-color) solid var(--sidebar-background-color);
			z-index: 10;
			top: 6px;
			position: relative;
		}
	}

	.container {
		cursor: pointer;
		margin: 0 32px 0 52px;
		padding: 12px;
		border-radius: var(--border-radius);
		border-top: 2px solid var(--table-row-border-color);

		&:hover {
			background-color: var(--highlight);
		}

		.title {
			margin-bottom: 4px;
			&::first-letter {
				text-transform: uppercase;
			}
		}

		.content {
			display: flex;
			align-items: center;

			.display {
				margin-right: 4px;
				font-weight: 500;
				color: var(--note-text-color);
			}

			.time {
				color: var(--note-text-color);
			}
		}
	}
}
</style>
