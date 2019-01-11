<template>
  <div id="calendar">
    <div id="header">
      <div id="header-start">
        <div id="date" ref="dropdown">
          {{monthNames[date.getMonth()]}} {{date.getFullYear()}}
          <transition name="months">
            <div v-show="showMonthSelect" id="date-select">
              <div id="date-header">
                <i class="material-icons" @click="decreaseYear()">arrow_back</i>
                {{date.getFullYear()}}
                <i class="material-icons" @click="increaseYear()">arrow_forward</i>
              </div>
              <div id="date-months">
                <div v-for="i in 12"
                  @click="setMonth(monthDistance - date.getMonth() + (i-1))"
                  :class="date.getMonth()+1 == i? 'mark-month' : '' "
                >
                  {{monthNames[i-1].substr(0, 3)}}
                </div>
              </div>
            </div>
          </transition>
        </div>
        <div id="arrows">
          <i class="material-icons icon" @click="decreaseMonth()">arrow_back</i>
          <i class="material-icons icon" @click="increaseMonth()">arrow_forward</i>
        </div>
      </div>
      <div id="header-end">
        <div
        id="today"
        class="button style-btn"
        :class="{'hidden': monthDistance == 0}"
        @click="resetMonth()">
          today
        </div>
      </div>
    </div>
    <div id="display">
      <transition :name="swipeTo">
        <Calendar
          :key="monthDistance"
          :month="monthDistance"
          :items="items"
          @day="openDay"
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
  props: ['items'],
  components: {
    Calendar,
    Popup
  },
  data () {
    return {
      monthDistance: 0,
      swipeTo: "left",
      showPopup: false,
      popupDate: new Date(),
      showMonthSelect: false,
      monthNames: ["january", "february", "march", "april", "may", "june",
        "july", "august", "september", "october", "november", "december"
      ],
      weekNames: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
    }
  },
  mixins: [mixin],
  computed: {
    date() {
      var date = new Date();
      date = new Date(date.getFullYear(), date.getMonth() + this.monthDistance);
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
      this.swipeTo = this.monthDistance > 0? "left" : "right";
      this.monthDistance = 0;
    },
    setMonth(index) {
      this.swipeTo = this.monthDistance - index > 0? "left" : "right";
      this.monthDistance = index;
    },
    openDay(date) {
      this.showPopup = true;
      this.popupDate = date;
    },
    documentClick(event) {
      var dropdown = this.$refs.dropdown;
      var target = event.target;
      this.showMonthSelect = (target === dropdown && !this.showMonthSelect) || (dropdown.contains(target) && target !== dropdown );
    },
    pressEsc(event) {
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
      if(event.deltaY > 0) {
        this.increaseMonth();
      } else {
        this.decreaseMonth();
      }
    }
  },
  created() {
      document.addEventListener('click', this.documentClick);
      document.addEventListener('keypress', this.pressEsc);
  },
  destroyed() {
      document.removeEventListener('click', this.documentClick);
      document.removeEventListener('keypress', this.pressEsc);
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
    text-align: center;
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
