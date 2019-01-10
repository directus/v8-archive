<template>
  <div class="day" :class="{hidden, today}">
    <div class="header">
      <div v-if="isWeek" class="header-week">{{week}}</div>
      <div class="header-day">{{date}}</div>
    </div>
    <div class="events">
      <a v-for="event in eventList()" @click.stop="event.to?$router.push(event.to):''">
        <div class="event"
          :class="event.id==-1?'event-more':''"
          @click="event.id==-1?$emit('popup') : ''"
        >
          {{event.title}}
        </div>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  props: ['week','display', 'date', 'events'],
  data () {
    return {

    }
  },
  computed: {
    hidden() {
      return (this.display == "hidden");
    },
    today() {
      return (this.display == "today");
    },
    isWeek() {
      return (this.week != null);
    },

  },
  methods: {
    eventList() {
      var events = this.events;
      var height = (window.innerHeight - 120) / 6;
      height -= 32;
      if(this.isWeek) {
        height -= 15;
      }
      if(this.today) {
        height -= 5;
      }

      var space = Math.floor(height/22);

      if(events.length > space) {
        events = events.slice(0, space - 1);
        events.push({
          'id': -1,
          'title': `and ${this.events.length - space + 1} more`,
        })
      }
      return events;
    }
  }
};
</script>

<style type="scss" scoped>

.today .header {
  padding: 5px 5px;
}

.today .header-day {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid var(--accent);
  color: var(--accent);
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
}
.events {

}

.event {
  display: flex;
  justify-content: space-between;
  width: 90%;
  height: 20px;
  margin: 1px 5%;
  padding: 2px 6px;
  color: var(--white);
  cursor: pointer;
  background-color: var(--accent);
  border-radius: var(--border-radius);
  line-height: 14px;
}

.event-more {
  color: var(--dark-gray);
  background-color: transparent;
  justify-content: center;
}
</style>
