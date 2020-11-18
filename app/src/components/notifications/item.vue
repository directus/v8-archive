<template>
	<div class="notification" :class="item.color">
		<div class="icon-main" :style="{ backgroundColor: ringColor }">
			<v-icon v-if="!!item.iconMain" :color="iconColor" :name="item.iconMain" />
		</div>
		<div class="content">
			<div class="title selectable">{{ item.title }}</div>
			<div class="details selectable" v-html="detailHtml"></div>
		</div>
		<div class="icon-right" @click="actionClick">
			<v-icon v-if="!!item.iconRight" :name="item.iconRight" />
		</div>
	</div>
</template>
<script>
import { mapMutations } from 'vuex';
import { REMOVE_NOTIFICATION } from '@/store/mutation-types';
export default {
	name: 'VItem',
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	computed: {
		detailHtml() {
			return this.item.details !== undefined
				? this.$helpers.snarkdown(this.item.details)
				: '';
		},
		iconColor() {
			return this.item.color !== undefined ? `--${this.item.color}-500` : '--blue-grey-500';
		},
		ringColor() {
			return this.item.color !== undefined
				? `var(--${this.item.color}-100)`
				: 'var(--blue-grey-100)';
		}
	},
	methods: {
		...mapMutations('notifications', [REMOVE_NOTIFICATION]),
		startItemTimeout() {
			if (this.item.delay !== undefined && this.item.delay > 0) {
				setTimeout(() => this.removeItemFromStore(), this.item.delay);
			}
		},
		removeItemFromStore() {
			this.$store.commit(REMOVE_NOTIFICATION, this.item.id);
		},
		actionClick() {
			if (!(this.item.onClick instanceof Function)) {
				throw new Error('Notification callback is not a function');
			}
			this.item.onClick();
			this.removeItemFromStore();
		}
	},
	mounted() {
		this.startItemTimeout();
	}
};
</script>
<style lang="scss" scoped>
.notification {
	display: flex;
	align-items: center;
	min-height: 64px;
	width: 100%;
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: 5px;
	margin-bottom: 12px;
	padding: 10px;
	background-color: var(--page-background-color);
	&.red {
		.title {
			color: var(--red);
		}
	}

	&.green {
		.title {
			color: var(--green);
		}
	}

	&.amber {
		.title {
			color: var(--amber);
		}
	}

	&.blue {
		.title {
			color: var(--blue);
		}
	}
}

.icon-main {
	width: 40px;
	height: 40px;
	min-width: 40px;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	background-color: var(--input-icon-color);
}

.icon-right {
	width: 24px;
	height: 24px;
	margin-left: auto;
	color: var(--input-icon-color);
	transition: color var(--fast) var(--transition);
	cursor: pointer;

	&:hover {
		color: var(--page-text-color);
	}
}

.content {
	padding-left: 10px;
	padding-right: 10px;
	.title {
		color: var(--heading-text-color);
	}

	.details {
		color: var(--note-text-color);
	}
}
</style>
