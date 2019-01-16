<template>
  <div id="view">
    <Day
      v-for="i in 42"
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
import Day from "./Day.vue";

export default {
  props: ["month", "items"],
  components: {
    Day
  },
  data() {
    return {
      innerHeight: window.innerHeight
    };
  },

  computed: {
    date() {
      var date = new Date();
      date = new Date(
        Date.UTC(date.getFullYear(), date.getMonth() + this.month, 1)
      );
      return date;
    },
    monthBegin() {
      var date = new Date(
        Date.UTC(this.date.getFullYear(), this.date.getMonth(), 1)
      ).getDay();
      return date == 0 ? 7 : date;
    },
    monthLength() {
      return new Date(
        Date.UTC(this.date.getFullYear(), this.date.getMonth() + 1, 0)
      ).getDate();
    },
    today() {
      var date = new Date();
      return date.getDate();
    }
  },

  methods: {
    events(index) {
      var events = [];
      var currentDay = new Date(
        Date.UTC(
          this.date.getFullYear(),
          this.date.getMonth(),
          index - this.monthBegin + 1
        )
      );

      return this.$parent.eventsAtDay(currentDay);
    },

    renderWeek(index) {
      if (index < 8) {
        return this.$t(
          "layouts-calendar-weeks." + this.$parent.weekNames[index - 1]
        );
      } else {
        return null;
      }
    },

    renderDate(index) {
      var realDate = new Date(
        Date.UTC(
          this.date.getFullYear(),
          this.date.getMonth(),
          index - this.monthBegin + 1
        )
      );
      return realDate.getDate();
    },

    getDate(index) {
      var realDate = new Date(
        Date.UTC(
          this.date.getFullYear(),
          this.date.getMonth(),
          index - this.monthBegin + 1
        )
      );
      return realDate;
    },

    /*
     * calculates the display type for each day (hidden | today | default)
     */
    display(index) {
      if (
        index < this.monthBegin ||
        index >= this.monthBegin + this.monthLength
      ) {
        return "hidden";
      } else if (this.month == 0 && index - this.monthBegin + 1 == this.today) {
        return "today";
      } else if (index - this.monthBegin < this.monthLength) {
        return "default";
      }
    },
    updateHeight(target) {
      this.innerHeight = window.innerHeight;
    }
  },
  created() {
    this.updateHeight = _.throttle(this.updateHeight, 100);
    window.addEventListener("resize", () => {
      this.updateHeight();
    });
  },
  destroyed() {
    window.removeEventListener("resize", () => {
      this.updateHeight();
    });
  }
};
</script>

<style type="scss" scoped>
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
  border: 1px solid var(--lightest-gray);
  background-color: var(--lightest-gray);
}
</style>
