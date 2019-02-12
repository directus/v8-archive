<template>
  <div class="day" :class="{ hidden, today }">
    <div class="header">
      <div v-if="isWeek" class="header-week">{{ week.substr(0, 3) }}</div>
      <div class="header-day">{{ date }}</div>
    </div>
    <div class="events">
      <a
        v-for="event in eventList"
        @click.stop="event.to ? $router.push(event.to) : ''"
      >
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
export default {
  props: ["week", "display", "date", "events"],
  data() {
    return {};
  },
  computed: {
    hidden() {
      return this.display == "hidden";
    },
    today() {
      return this.display == "today";
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
          title: this.$t("layouts-calendar-moreEvents", {
            amount: this.events.length - space + 1
          }),
          time: ""
        });
      }
      return events;
    }
  }
};
</script>

<style lang="scss" type="scss" scoped>
.today .header {
  padding: 5px 5px;
}

.today .header-day {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid var(--accent);
  color: var(--accent);
  line-height: 0;
}

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
  padding: 5px 5px 0px 5px;
}

.header-week {
  width: 32px;
  text-align: center;
  font-size: 0.7em;
  color: var(--light-gray);
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
  color: var(--dark-gray);
  background-color: transparent;
  justify-content: center;
}
</style>
