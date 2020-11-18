<template>
	<div class="day">
		<div class="date type-heading-small">
			Activity on {{ $t('months.' + $parent.monthNames[date.getMonth()]) }}
			{{ date.getDate() }},
			{{ date.getFullYear() }}
		</div>
		<div class="events">
			<Event
				v-for="(event, index) in events"
				:key="index"
				:data="event"
				:connect="index < events.length - 1"
			/>
		</div>
	</div>
</template>

<script>
import Event from './Event.vue';

export default {
	components: {
		Event
	},
	props: {
		date: {
			type: Date,
			default: new Date()
		},
		events: {
			type: Array,
			default: null
		}
	},
	data() {
		return {};
	},
	computed: {},
	created() {},
	mounted() {},
	destroyed() {},
	methods: {}
};
</script>

<style lang="scss" scoped>
.day {
	margin-bottom: 10px;

	.date {
		padding: 19px 32px; // 19 visually aligns it with the project switcher
		border-bottom: 2px solid var(--table-row-border-color);
		position: sticky;
		top: var(--header-height);
		background-color: var(--page-background-color);
		z-index: 2;
	}

	.events {
		padding: 15px 0px;
	}
}
</style>
