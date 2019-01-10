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
  props: ['month', 'items'],
  components: {
    Day
  },
  data() {
    return {
    }
  },

  computed: {
    date() {
      var date = new Date();
      date = new Date(date.getFullYear(), date.getMonth() + this.month);
      return date;
    },
    monthBegin() {
      var date = new Date(this.date.getFullYear(), this.date.getMonth(), 1).getDay();
      return date == 0 ? 7 : date;
    },
    monthEnd() {
      return new Date(this.date.getFullYear(), this.date.getMonth() +1, 0);
    },
    monthLength() {
      return new Date(this.date.getFullYear(), this.date.getMonth()+1, 0).getDate();
    },
    today() {
      var date = new Date();
      return date.getDate();
    }
  },

  methods: {
    events(index) {
      var events = [];
      var currentDay = new Date(this.date.getFullYear(), this.date.getMonth(), index - this.monthBegin + 1);
      // console.log(`position: ${index}\ncalculated: \n ${currentDay}`);

      var dateId = this.$parent.viewOptions.date;
      var titleId = this.$parent.viewOptions.title;
      if(!dateId || !titleId)return;

      // console.log(`-----Compare events------`);

      for (var i = 0; i < this.items.length; i++) {
        var item = this.items[i];
        var date = new Date(item[dateId]+"T00:00:00");

        if(!date)continue;

        if(this.isSameDay(date, currentDay)){
          var event = {'id': item.id, 'title': item[titleId], 'to': item.__link__};
          events.push(event);
        }
      }
      return events;
    },

    isSameDay(date1, date2){
      return date1.getFullYear() == date2.getFullYear() &&
        date1.getMonth() == date2.getMonth() &&
        date1.getDate() == date2.getDate()
    },

    renderWeek(index) {
      if(index < 8){
        return this.$parent.weekNames[index-1];
      } else {
        return null;
      }
    },

    renderDate(index) {

      var realDate = new Date(this.date.getFullYear(), this.date.getMonth(), index - this.monthBegin + 1);
      return realDate.getDate();
    },

    getDate(index) {
      var realDate = new Date(this.date.getFullYear(), this.date.getMonth(), index - this.monthBegin + 1);
      return realDate;
    },

    display(index) {
      if(index < this.monthBegin || index >= this.monthBegin + this.monthLength) {
        return "hidden";
      } else if(this.month == 0 && index - this.monthBegin + 1 == this.today) {
        return "today";
      } else if(index - this.monthBegin < this.monthLength){
        return "full";
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
