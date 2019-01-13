<template>
  <transition name="popup">
  <div v-show="open" id="wrapper">
    <div id="background" @click="close()"></div>
    <div id="popup">
      <div id="header">
        <span>{{$t("layouts-calendar-events")}}</span>
        <i class="material-icons" @click="close()">close</i>
      </div>
      <div id="sidebar-header">
        {{$t("layouts-calendar-months." + $parent.monthNames[date.getMonth()])}} {{date.getFullYear()}}
      </div>
      <div id="sidebar" @wheel="scroll">
        <transition :name="moveSidebar">
          <div :key="days" id="dates-container">
            <div v-for="day in days" class="dates" @click="changeDay(day.index)">
              <span class="dates-day">{{weekname(day.date.getDay())}}</span>
              <span class="dates-date">{{day.date.getDate()}}</span>
              <div :class="getEventCount(day.date) > 0? 'date-counter' : 'date-counter-hidden'">
                {{getEventCount(day.date)}}
              </div>
            </div>
          </div>
        </transition>
      </div>
      <div id="events">
        <div
          v-for="event in events"
          class="event"
          :style="event.color"
          @click="$router.push(event.to)"
        >
          <span>{{event.title}}</span>
          <span>{{event.time.substr(0, 5)}}</span>
        </div>
        <div v-if="getEventCount(date) == 0" id="events-none">
          <span>{{randomEmoji()}}</span><br><br>
          <span>{{$t("layouts-calendar-noEvents")}}</span>

        </div>
      </div>
      <a id="add" :href="addItemURL">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div>
</transition>
</template>

<script>
export default {
  props: ['open', 'date'],
  components: {
  },
  data() {
    return {
      moveSidebar: "move-0",
    }
  },
  computed: {
    days() {
      var days = new Array();

      for (var i = -4; i <= 4; i++) {
        var date = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + i);
        days.push({'date': date, 'index': i});
      }
      return days;
    },
    events() {
      return this.$parent.eventsAtDay(this.date);
    },
    addItemURL() {
      var url = this.$root._router.currentRoute.path;
      return "#"+url+"/+";
    }
  },
  methods: {
    weekname(day) {
      return this.$t("layouts-calendar-weeks." + this.$parent.weekNames[day == 0? 6 : day-1]).substr(0, 3);
    },
    changeDay(distance) {
      this.moveSidebar = "move-"+distance;
      var newDate = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + distance);
      this.date = newDate;
      this.$parent.popupDate = newDate;
    },

    close() {
      this.$emit('close');
      this.moveSidebar = "move-0";
    },

    scroll(event) {
      if(event.deltaY > 0) {
        this.changeDay(1);
      } else {
        this.changeDay(-1);
      }
    },

    getEventCount(date) {
      var events = 0;
      var dateId = this.$parent.viewOptions.date;

      if(!dateId)return;

      for (var i = 0; i < this.$parent.items.length; i++) {
        var item = this.$parent.items[i];
        var eventDate = new Date(item[dateId]);

        if(this.$parent.isSameDay(date, eventDate)){
          events++;
        }
      }
      return events;
    },
    randomEmoji() {
      switch (Math.round(Math.random()*5)) {
        case 1:
          return "(≧︿≦)";
        case 2:
          return "¯\\(°_o)/¯";
        case 2:
          return "(⌐⊙_⊙)";
        case 3:
          return "( º﹃º )";
        case 4:
          return "¯\\_(ツ)_/¯";
        case 5:
          return "(·.·)";
        default:
          return "\\(°Ω°)/"
      }
    }
  }
};
</script>

<style type="scss" scoped>

#wrapper {
  display: flex;
  align-items: center;
  position: absolute;
  justify-content: center;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transition: opacity 300ms;
}
#background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(38, 50, 56, 0.9);
  z-index: 30;
}

.popup-enter {
  opacity: 0;
}

.popup-enter-to {
  z-index: 30;
}

.popup-leave-to {
  opacity: 0;
  z-index: 30;
}

.popup-enter-active .popup-leave-active {
  opacity: 1;
}

#popup {
  display: grid;
  grid-template-columns: 25% 75%;
  grid-template-rows: 10% 90%;
  position: relative;
  z-index: 30;
  overflow: hidden;
  background-color: var(--white);
  width: 50%;
  height: 75%;
  border-radius: var(--border-radius)
}

#header {
  padding: 20px;
  grid-column: 2 / 3;

}

#header span {
  font-size: 2em;
  font-weight: 400;
}

#header i {
  cursor: pointer;
  float: right;
}

#sidebar-header {
  display: flex;
  justify-content: center;
  align-items: center;
  grid-column: 1 / 2;
  grid-row: 1 / 2;
  background-color: var(--lightest-gray);
  font-size: 1.5em;
  font-weight: 400;
  text-transform: capitalize;
}

#sidebar {
  grid-column: 1 / 2;
  grid-row: 2 / 3;
  background-color: var(--lightest-gray);
  height: 100%;
  overflow: hidden;
}

#dates-container {
  margin-top: calc((67.5vh / 5) * -2);
  transition: transform 300ms;
}

.move-1-enter {
  transform: translateY(calc(100% / 9));
}
.move-1-leave-to {
  transform: translateY(calc(100% / -9));
}

.move-2-enter {
  transform: translateY(calc(100% / 9 * 2));
}
.move-2-leave-to {
  transform: translateY(calc(100% / -9 * 2));
}

.move--1-enter {
  transform: translateY(calc(100% / -9));
}
.move--1-leave-to {
  transform: translateY(calc(100% / 9));
}

.move--2-enter {
  transform: translateY(calc(100% / -9 * 2));
}
.move--2-leave-to {
  transform: translateY(calc(100% / 9 * 2));
}

.move-1-enter-active .move-1-leave-active
.move-2-enter-active .move-2-leave-active
.move--1-enter-active .move--1-leave-active
.move--2-enter-active .move--2-leave-active {
  transform: translateY(0);
}

.dates {
  display: flex;
  position: relative;
  flex-direction: column;
  justify-content: center;
  width: 100%;
  height: calc(67.5vh / 5);
  padding: 0 30%;
  color: var(--light-gray);
  cursor: pointer;
}

.dates:nth-child(5) {
  color: var(--dark-gray);
}

.dates::after {
  content: '';
  position: absolute;
  width: 60%;
  height: 2px;
  background-color: var(--lighter-gray);
  border-radius: 2px;
  bottom: 0;
  left: 50%;
  transform: translate(-50%);
}

.dates-day {
  font-size: 1.2em;
  font-weight: 400;
}

.dates-date {
  font-size: 5em;
  line-height: 0.9em;
  align-self: center;
}

.date-counter-hidden {
  width: 7px;
  height: 7px;
  opacity: 0;
}

.date-counter {
  display: flex;
  justify-content: center;
  align-self: center;
  width: 20px;
  height: 20px;
  line-height: 20px;
  border-radius: 50%;
  color: var(--white);
  background-color: var(--accent);
}

#events {
  grid-row: 2 / 3;
  display: flex;
  float: right;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

#events-none {
  text-align: center;
  color: var(--lighter-gray);
  font-size: 1.5em;
}

#events-none span:nth-child(1) {
  font-size: 3em;
}

.event {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 90%;
  height: 40px;
  border-radius: 3px;
  margin: 5px 0;
  padding: 2px 15px;
  color: var(--white);

  font-size: 1.2em;
  font-weight: 400;
  cursor: pointer;
}

#add {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  bottom: 20px;
  right: 20px;
  width: 50px;
  height: 50px;
  box-shadow: 1px 1px 4px 0px gray;
  border-radius: 50%;
  background-color: var(--accent);
  text-decoration: none;
  color: var(--white);
  cursor: pointer;
}

#add i {
  font-size: 2.5em;
}
</style>
