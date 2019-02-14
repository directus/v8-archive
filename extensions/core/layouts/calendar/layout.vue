<template>
  <div id="calendar">
    <div id="header">
      <div id="header-start">
        <div id="date" ref="dropdown">
          {{ $t("layouts-calendar-months." + monthNames[date.getMonth()]) }}
          {{ date.getFullYear() }}
          <transition name="months">
            <div v-show="showMonthSelect" id="date-select">
              <div id="date-header">
                <i class="material-icons" @click="decreaseYear()">arrow_back</i>
                {{ date.getFullYear() }}
                <i class="material-icons" @click="increaseYear()"
                  >arrow_forward</i
                >
              </div>
              <div id="date-months">
                <div
                  v-for="i in 12"
                  @click="setMonth(monthDistance - date.getMonth() + (i - 1))"
                  :class="date.getMonth() + 1 == i ? 'mark-month' : ''"
                >
                  {{ monthNames[i - 1].substr(0, 3) }}
                </div>
              </div>
            </div>
          </transition>
        </div>
        <div id="arrows">
          <i class="material-icons icon" @click="decreaseMonth()">arrow_back</i>
          <i class="material-icons icon" @click="increaseMonth()"
            >arrow_forward</i
          >
        </div>
      </div>
      <div id="header-end">
        <div
          id="today"
          class="button style-btn"
          :class="{ hidden: monthDistance == 0 }"
          @click="resetMonth()"
        >
          {{ $t("layouts-calendar-today") }}
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
      :date="popupDate"
      @close="showPopup = false"
    ></Popup>
  </div>
</template>

<script>
import mixin from "../../../mixins/layout";
import Calendar from "./Calendar.vue";
import Popup from "./Popup.vue";

export default {
  props: ["items"],
  components: {
    Calendar,
    Popup
  },
  data() {
    return {
      //the distance (in months) of the current month
      monthDistance: 0,

      //animates the calendar swipe animation in the right direction
      swipeTo: "left",

      showPopup: false,

      popupDate: new Date(),

      showMonthSelect: false,

      monthNames: [
        "january",
        "february",
        "march",
        "april",
        "may",
        "june",
        "july",
        "august",
        "september",
        "october",
        "november",
        "december"
      ],
      weekNames: [
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday",
        "sunday"
      ]
    };
  },
  mixins: [mixin],
  computed: {
    // Get the date of the view based on the delta of the months that the user
    // has scrolled
    date() {
      var date = new Date();
      date = new Date(date.getFullYear(), date.getMonth() + this.monthDistance, 1);
      return date;
    }
  },
  methods: {
    increaseYear() {
      this.swipeTo = "right";
      this.monthDistance += 12;
    },

    decreaseYear() {
      this.swipeTo = "left";
      this.monthDistance -= 12;
    },

    increaseMonth() {
      this.swipeTo = "right";
      this.monthDistance++;
    },

    decreaseMonth() {
      this.swipeTo = "left";
      this.monthDistance--;
    },

    resetMonth() {
      this.swipeTo = this.monthDistance > 0 ? "left" : "right";
      this.monthDistance = 0;
    },

    setMonth(index) {
      this.swipeTo = this.monthDistance - index > 0 ? "left" : "right";
      this.monthDistance = index;
    },

    openPopup(date) {
      this.showPopup = true;
      this.popupDate = date;
    },

    eventsAtDay(date) {
      var events = [];
      var dateId = this.viewOptions.date;
      var titleId = this.viewOptions.title;
      var timeId = this.viewOptions.time;
      var colorId = this.viewOptions.color;

      if (!dateId || !titleId) return;

      for (var i = 0; i < this.$parent.items.length; i++) {

        var item = this.$parent.items[i];
        var eventDate = new Date(item[dateId] + "T00:00:00");
        var time = item[timeId] && timeId != 0 ? item[timeId] : "";
        var color = item[colorId];

        if (!eventDate) continue;

        if (!color) color = "accent";
        color = `background-color: var(--${color})`;

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

      if (time1[timeId] == "" && time2[timeId] == "") return 0;
      if (time1[timeId] != "" && time2[timeId] == "") return -1;
      if (time1[timeId] == "" && time2[timeId] != "") return 1;

      var timeA = new Date("1970-01-01T" + time1[timeId]);
      var timeB = new Date("1970-01-01T" + time2[timeId]);

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
        case "Escape":
          this.showPopup = false;
          break;
        case "ArrowRight":
          this.increaseMonth();
          break;
        case "ArrowLeft":
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
  },
  created() {
    this.scroll = _.throttle(this.scroll, 200);
    document.addEventListener("click", this.documentClick);
    document.addEventListener("keypress", this.keyPress);
  },
  destroyed() {
    document.removeEventListener("click", this.documentClick);
    document.removeEventListener("keypress", this.keyPress);
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
  width: var(--width-small);
  border-radius: var(--border-radius);
  border: var(--input-border-width) solid var(--gray);
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
  background-color: var(--lighter-gray);
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
  color: var(--darker-gray);
  /* border: 1px solid var(--dark-gray);
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
  color: var(--dark-gray);
  text-transform: uppercase;
}

#display {
  position: relative;
  width: 100%;
  height: calc(100% - var(--header-height));
  overflow: hidden;
}
</style>
