<template>
	<div id="view">
		<!-- 42 = 6 rows * 7 days -->
		<Day
			v-for="i in 42"
			:key="i"
			:display="display(i)"
			:week="renderWeek(i)"
			:date="renderDate(i)"
			:events="events(i)"
			@click.native="$emit('day', getDate(i))"
			@popup="$emit('day', getDate(i))"
		></Day>
	</div>
</template>

<script>
import Day from './Day.vue';
import { throttle } from 'lodash';

export default {
	components: {
		Day
	},
	props: ['month', 'items'],
	data() {
		return {
			innerHeight: window.innerHeight
		};
	},

	computed: {
		date() {
			var date = new Date();
			date = new Date(date.getFullYear(), date.getMonth() + this.month, 1);
			return date;
		},
		monthBegin() {
			var date = new Date(this.date.getFullYear(), this.date.getMonth(), 1).getDay();
			return date == 0 ? 7 : date;
		},
		monthLength() {
			return new Date(this.date.getFullYear(), this.date.getMonth() + 1, 0).getDate();
		},
		today() {
			var date = new Date();
			return date.getDate();
		}
	},
	created() {
		this.updateHeight = throttle(this.updateHeight, 100);
		window.addEventListener('resize', () => {
			this.updateHeight();
		});
	},
	destroyed() {
		window.removeEventListener('resize', () => {
			this.updateHeight();
		});
	},

	methods: {
		events(index) {
			var currentDay = new Date(
				this.date.getFullYear(),
				this.date.getMonth(),
				index - this.monthBegin + 1
			);

			return this.$parent.eventsAtDay(currentDay);
		},

		renderWeek(index) {
			if (index < 8) {
				return this.$t('weeks.' + this.$parent.weekNames[index - 1]);
			} else {
				return null;
			}
		},

		renderDate(index) {
			var realDate = new Date(
				this.date.getFullYear(),
				this.date.getMonth(),
				index - this.monthBegin + 1
			);
			return realDate.getDate();
		},

		getDate(index) {
			var realDate = new Date(
				this.date.getFullYear(),
				this.date.getMonth(),
				index - this.monthBegin + 1
			);
			return realDate;
		},

		/*
		 * calculates the display type for each day (hidden | today | default)
		 */
		display(index) {
			if (index < this.monthBegin || index >= this.monthBegin + this.monthLength) {
				return 'hidden';
			} else if (this.month == 0 && index - this.monthBegin + 1 == this.today) {
				return 'today';
			} else if (index - this.monthBegin < this.monthLength) {
				return 'default';
			}
		},
		updateHeight() {
			this.innerHeight = window.innerHeight;
		}
	}
};
</script>

<style lang="scss" scoped>
.hidden {
	opacity: 0.5;
	cursor: default !important;
}

#view {
	transition: transform 500ms;
	position: absolute;
	height: 100%;
	width: 100%;
	display: grid;
	grid-template: repeat(6, 1fr) / repeat(7, 1fr);
	grid-gap: 1px;
	border: 1px solid var(--blue-grey-50);
	background-color: var(--blue-grey-50);
}
</style>
