<template>
	<div class="day" :class="{ hidden, today }">
		<div class="header">
			<div v-if="isWeek" class="header-week">{{ week.substr(0, 3) }}</div>
			<div class="header-day">{{ date }}</div>
		</div>
		<div class="events">
			<a v-for="event in eventList" :key="event.id" @click.stop="goToItem(event.id)">
				<div
					class="event"
					:class="event.id == -1 ? 'event-more' : ''"
					:style="event.color"
					@click="event.id == -1 ? $emit('popup') : ''"
				>
					<span class="title">{{ event.title }}</span>
					<span class="time">{{ event.time.substr(0, 5) }}</span>
				</div>
			</a>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
	props: {
		week: {
			type: String,
			default: null
		},
		display: {
			type: String,
			required: true
		},
		date: {
			type: Number,
			required: true
		},
		events: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {};
	},
	computed: {
		...mapState(['currentProjectKey']),
		hidden() {
			return this.display == 'hidden';
		},
		today() {
			return this.display == 'today';
		},
		isWeek() {
			return this.week != null;
		},
		eventList() {
			if (!this.events) return;

			var events = this.events;
			var height = (this.$parent.innerHeight - 120) / 6;
			height -= 32;
			if (this.isWeek) {
				height -= 15;
			}
			if (this.today) {
				height -= 5;
			}

			var space = Math.floor(height / 22);

			if (events.length > space) {
				events = events.slice(0, space - 1);
				events.push({
					id: -1,
					title: this.$t('layouts.calendar.moreEvents', {
						amount: this.events.length - space + 1
					}),
					time: ''
				});
			}
			return events;
		}
	},
	methods: {
		goToItem(id) {
			if (id !== -1)
				this.$router.push(
					`/${this.currentProjectKey}/collections/${this.$parent.$parent.collection}/${id}`
				);
		}
	}
};
</script>

<style lang="scss" scoped>
.day {
	overflow: hidden;
	background-color: var(--white);
	max-height: 100%;
}

.day:hover {
	background-color: var(--highlight);
}

.header {
	width: 100%;
	font-size: 1.3em;
	font-weight: 400;
	padding: 10px 5px;
}

.header-week {
	width: 32px;
	text-align: center;
	font-size: 0.7em;
	color: var(--blue-grey-300);
}

.header-day {
	width: 100%;
	width: 32px;
	height: 32px;
	display: flex;
	align-items: center;
	justify-content: center;
	padding-right: 1px;
}
.today .header-day {
	border-radius: 50%;
	border: 2px solid var(--blue-grey-900);
	color: var(--blue-grey-900);
}

.events {
	.event {
		display: flex;
		justify-content: space-between;
		width: 90%;
		height: 20px;
		margin: 0 5% 2px;
		padding: 2px 6px;
		color: var(--white);
		cursor: pointer;
		border-radius: var(--border-radius);
		line-height: 14px;
		.title {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	}
}

.event-more {
	.title {
		color: var(--blue-grey-600);
	}
	background-color: transparent;
	justify-content: center;
}
</style>
