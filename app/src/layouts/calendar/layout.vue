<template>
	<div id="calendar">
		<div id="header">
			<div id="header-start">
				<div id="date" ref="dropdown">
					{{ $t('months.' + monthNames[date.getMonth()]) }}
					{{ date.getFullYear() }}
					<transition name="months">
						<div v-show="showMonthSelect" id="date-select">
							<div id="date-header">
								<button @click="decreaseYear()">
									<v-icon name="arrow_back" />
								</button>
								{{ date.getFullYear() }}
								<button @click="increaseYear()">
									<v-icon name="arrow_forward" />
								</button>
							</div>
							<div id="date-months">
								<div
									v-for="i in 12"
									:key="i"
									:class="date.getMonth() + 1 == i ? 'mark-month' : ''"
									@click="setMonth(monthDistance - date.getMonth() + (i - 1))"
								>
									{{ monthNames[i - 1].substr(0, 3) }}
								</div>
							</div>
						</div>
					</transition>
				</div>
				<div id="arrows">
					<button @click="decreaseMonth()">
						<v-icon class="icon" name="arrow_back" />
					</button>
					<button @click="increaseMonth()">
						<v-icon class="icon" name="arrow_forward" />
					</button>
				</div>
			</div>
			<div id="header-end">
				<div
					id="today"
					class="button"
					:class="{ hidden: monthDistance == 0 }"
					@click="resetMonth()"
				>
					{{ $t('layouts.calendar.today') }}
				</div>
			</div>
		</div>
		<div id="display">
			<transition :name="swipeTo">
				<Calendar
					:key="monthDistance"
					:month="monthDistance"
					:items="items"
					@day="openPopup"
					@wheel.native="scroll"
				></Calendar>
			</transition>
		</div>
		<Popup
			:open="showPopup"
			:parentdate="popupDate"
			:parentevents="events"
			@close="showPopup = false"
		></Popup>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/layout';
import Calendar from './Calendar.vue';
import Popup from './Popup.vue';
import { throtle } from 'lodash';

export default {
	components: {
		Calendar,
		Popup
	},
	mixins: [mixin],
	props: ['items'],
	data() {
		return {
			//the distance (in months) of the current month
			monthDistance: 0,

			//animates the calendar swipe animation in the right direction
			swipeTo: 'left',

			showPopup: false,

			popupDate: new Date(),

			events: [],

			showMonthSelect: false,

			monthNames: [
				'january',
				'february',
				'march',
				'april',
				'may',
				'june',
				'july',
				'august',
				'september',
				'october',
				'november',
				'december'
			],
			weekNames: [
				'monday',
				'tuesday',
				'wednesday',
				'thursday',
				'friday',
				'saturday',
				'sunday'
			]
		};
	},
	computed: {
		// Get the date of the view based on the delta of the months that the user
		// has scrolled
		date() {
			var date = new Date();
			date = new Date(date.getFullYear(), date.getMonth() + this.monthDistance, 1);
			return date;
		}
	},
	watch: {
		date(newValue) {
			this.getData(newValue);
		},
		viewOptions() {
			this.getData(this.date);
		}
	},
	created() {
		this.getData(this.date);
		this.scroll = throttle(this.scroll, 200);
		document.addEventListener('click', this.documentClick);
		document.addEventListener('keypress', this.keyPress);
	},
	destroyed() {
		document.removeEventListener('click', this.documentClick);
		document.removeEventListener('keypress', this.keyPress);
	},
	methods: {
		getData(date) {
			var dateId = this.viewOptions.date;
			var datetimeId = this.viewOptions.datetime;
			var columnName = '';
			if (datetimeId && datetimeId !== '__none__') {
				columnName = datetimeId;
			} else {
				columnName = dateId;
			}
			if (!columnName || columnName === '__none__') return;

			var endOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
			var from =
				date.getFullYear() +
				'-' +
				(date.getMonth() + 1) +
				'-' +
				date.getDate() +
				' ' +
				date.getHours() +
				':' +
				date.getMinutes() +
				':' +
				date.getSeconds();
			var to =
				endOfMonth.getFullYear() +
				'-' +
				(endOfMonth.getMonth() + 1) +
				'-' +
				endOfMonth.getDate() +
				' ' +
				endOfMonth.getHours() +
				':' +
				endOfMonth.getMinutes() +
				':' +
				endOfMonth.getSeconds();
			let filter = {
				[columnName]: {
					between: from + ',' + to
				}
			};
			this.$api
				.getItems(this.$parent.collection, {
					fields: '*.*.*',
					filter: filter
				})
				.then(res => {
					res.data.forEach(item => {
						item.to = 'test';
					});
					this.events = res.data;
				})
				.catch(e => {
					console.log(e);
				});
		},
		increaseYear() {
			this.swipeTo = 'right';
			this.monthDistance += 12;
		},

		decreaseYear() {
			this.swipeTo = 'left';
			this.monthDistance -= 12;
		},

		increaseMonth() {
			this.swipeTo = 'right';
			this.monthDistance++;
		},

		decreaseMonth() {
			this.swipeTo = 'left';
			this.monthDistance--;
		},

		resetMonth() {
			this.swipeTo = this.monthDistance > 0 ? 'left' : 'right';
			this.monthDistance = 0;
		},

		setMonth(index) {
			this.swipeTo = this.monthDistance - index > 0 ? 'left' : 'right';
			this.monthDistance = index;
		},

		openPopup(date) {
			this.showPopup = true;
			this.popupDate = date;
		},

		eventsAtDay(date) {
			var events = [];
			var dateId = this.viewOptions.date;
			var datetimeId = this.viewOptions.datetime;
			var titleId = this.viewOptions.title;
			var timeId = this.viewOptions.time;
			var colorId = this.viewOptions.color;

			if (!(dateId || datetimeId) || !titleId) return;

			for (var i = 0; i < this.events.length; i++) {
				var item = this.events[i];
				var eventDate = '',
					time = '';

				// datetime first
				if (datetimeId && datetimeId !== '__none__') {
					eventDate = new Date(item[datetimeId]);
					// allow to overridetime of datetime if time field is set
					if (timeId === '__none__') time = item[datetimeId].slice(-8);
					else time = item[timeId] && timeId != 0 ? item[timeId] : '';
				} else {
					eventDate = new Date(item[dateId] + 'T00:00:00');
					time = item[timeId] && timeId != 0 ? item[timeId] : '';
				}
				if (!eventDate) continue;

				var color = item[colorId] || 'accent';
				var colorOutput = 'background-color: ';

				if (color.match(/^\d{1,3},\d{1,3},\d{1,3}$/)) {
					colorOutput += `rgb(${color})`;
				} else if (color.match(/^#[0-9a-f]{3,6}$/i)) {
					colorOutput += color;
				} else {
					colorOutput += `var(--${color})`;
				}
				color = colorOutput;

				if (this.isSameDay(date, eventDate)) {
					var event = {
						id: item.id,
						title: item[titleId],
						to: item.__link__,
						time,
						color
					};

					events.push(event);
				}
			}

			if (timeId != 0) {
				events.sort(this.compareTime);
			}
			return events;
		},

		compareTime(time1, time2) {
			var timeId = this.viewOptions.time;

			if (time1[timeId] == '' && time2[timeId] == '') return 0;
			if (time1[timeId] != '' && time2[timeId] == '') return -1;
			if (time1[timeId] == '' && time2[timeId] != '') return 1;

			var timeA = new Date('1970-01-01T' + time1[timeId]);
			var timeB = new Date('1970-01-01T' + time2[timeId]);

			if (timeA > timeB) return 1;
			if (timeA < timeB) return -1;
			return 0;
		},

		isSameDay(date1, date2) {
			return (
				date1.getFullYear() == date2.getFullYear() &&
				date1.getMonth() == date2.getMonth() &&
				date1.getDate() == date2.getDate()
			);
		},

		//opens or closes the date select dropdown
		documentClick(event) {
			var dropdown = this.$refs.dropdown;
			var target = event.target;
			this.showMonthSelect =
				(target === dropdown && !this.showMonthSelect) ||
				(dropdown.contains(target) && target !== dropdown);
		},

		keyPress(event) {
			switch (event.key) {
				case 'Escape':
					this.showPopup = false;
					break;
				case 'ArrowRight':
					this.increaseMonth();
					break;
				case 'ArrowLeft':
					this.decreaseMonth();
					break;
				default:
					break;
			}
		},

		scroll(event) {
			if (event.deltaY > 0) {
				this.increaseMonth();
			} else {
				this.decreaseMonth();
			}
		}
	}
};
</script>

<style type="scss" scoped>
.hidden {
	opacity: 0.5;
	cursor: default !important;
}
.left-enter {
	transform: translate(-100%);
}

.left-leave-to {
	transform: translate(100%);
}

.right-enter {
	transform: translate(100%);
}

.right-leave-to {
	transform: translate(-100%);
}

.left-enter-active .left-leave-active .right-enter-active .right-leave-active {
	transform: translate(0);
}
.button {
	height: 40px;
	width: 136px;
	border-radius: var(--border-radius);
	border: var(--input-border-width) solid var(--blue-grey-400);
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
}

#calendar {
	width: 100%;
	height: calc(100vh - 4.62rem);
}

#header {
	height: var(--header-height);
	padding: 0 20px;
	display: flex;
	align-items: center;
	justify-content: space-between;
}

#header-start {
	display: flex;
	width: 300px;
}

#date {
	cursor: pointer;
	font-size: 1.5em;
	font-weight: 400;
	width: 180px;
	text-transform: capitalize;
	position: relative;
	text-align: left;
}

.months-enter {
	opacity: 0;
}

.months-leave-to {
	opacity: 0;
}

.months-enter-active .months-leave-active {
	opacity: 1;
}

#date-select {
	padding: 8px 4px;
	position: absolute;
	top: 100%;
	width: 100%;
	z-index: 1;
	border-radius: var(--border-radius);
	background-color: var(--blue-grey-200);
	transition: opacity 100ms;
}

#date-header {
	display: flex;
	margin: 5px 0 10px 0;
	justify-content: space-around;
}

#date-header i {
	cursor: pointer;
}

.mark-month {
	font-weight: 500;
	color: var(--blue-grey-800);
	/* border: 1px solid var(--blue-grey-600);
    border-radius: var(--border-radius); */
}

#date-months {
	display: grid;
	grid-template: repeat(4, 1fr) / repeat(3, 1fr);
	font-size: 0.8em;
	line-height: 1.6em;
	text-align: center;
}

#date-months div {
	cursor: pointer;
}

#arrows i {
	padding: 0 5px;
	font-size: 2em;
	cursor: pointer;
}

#today {
	color: var(--blue-grey-600);
	text-transform: uppercase;
}

#display {
	position: relative;
	width: 100%;
	height: calc(100% - var(--header-height));
	overflow: hidden;
}
</style>
