<template>
    <div :class="['vue-star-rating']">
        <div @mouseleave="resetRating" class="vue-star-rating">
            <span v-for="n in Number(count)" :key="n" 
                :class="[{'vue-star-rating-pointer': !readonly && !disabled }, 'vue-star-rating-star']" 
                :style="{'margin-right': margin + 'px'}">

              <star :fill="fillLevel[n-1]" 
                :star-id="n" :active-color="activeColor" :inactive-color="inactiveColor" 
                :border-color="fillLevel[n-1] ? borderColor : inactiveBorderColor" 
                @star-selected="setRating($event, true)" @star-mouse-move="setRating"/>

            </span>
            <div class="vue-star-rating-box">
                <p>
                    <v-input 
                      type="text"
                      v-model="currentRating" 
                      class="vue-star-rating-text" 
                      :disabled="disabled || readonly" @input="addEvent"></v-input>
                      <span>out of {{count}} stars</span>
                </p>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
import star from "./star.vue";
export default {
  components: {
    star
  },
  model: {
    prop: "rating",
    event: "rating-selected"
  },
  props: {
    readonly: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    rating: {
      type: Number,
      default: 0
    },
    count: {
      type: Number,
      default: 5
    },
    color: {
      type: String,
      default: "blue"
    }
  },
  created() {
    this.margin = 5;
    if (this.disabled) {
      this.rating = 0;
    }
    this.currentRating = this.rating;
    this.selectedRating = this.currentRating;

    this.createStars();
  },
  methods: {
    addEvent(val) {
      let rating = this.selectedRating;
      if (val.length == 1 && (val >= 0 && val <= 9)) {
        rating = val;
        return;
      }

      const regExp = new RegExp("[0-9].?[0-9]");
      if (val.length == 3 && regExp.test(val)) {
        rating = val;
      }

      if (rating <= this.count) {
        this.selectedRating = rating;
      }
    },
    setRating($event, persist) {
      if (!this.readonly && !this.disabled) {
        const position = $event.position / 100;
        this.currentRating = ($event.id + position - 1).toFixed(1);
        this.currentRating =
          this.currentRating > this.count ? this.count : this.currentRating;
        this.createStars();
        if (persist) {
          this.selectedRating = this.currentRating;
          this.$emit("rating-selected", this.selectedRating);
          this.ratingSelected = true;
        } else {
          this.$emit("current-rating", this.currentRating);
        }
      }
    },
    resetRating() {
      if (!this.readonly) {
        this.currentRating = this.selectedRating;
        this.createStars();
      }
    },
    createStars() {
      for (var i = 0; i < this.count; i++) {
        let level = 0;
        if (i < this.currentRating) {
          level =
            this.currentRating - i > 1 ? 100 : (this.currentRating - i) * 100;
        }

        this.$set(this.fillLevel, i, Math.round(level));
      }
    }
  },
  computed: {
    activeColor() {
      if (this.color == "blue") {
        return "#039be5";
      } else {
        return "#ffd055";
      }
    },
    inactiveColor() {
      return "#ffffff";
    },
    borderColor() {
      if (this.color == "blue") {
        return "#039be5";
      } else {
        return "#ffd055";
      }
    },
    inactiveBorderColor() {
      return "#999";
    }
  },
  watch: {
    rating(val) {
      this.currentRating = val;
      this.selectedRating = val;
      this.createStars();
    }
  },
  data() {
    return {
      fillLevel: [],
      currentRating: 0,
      selectedRating: 0,
      ratingSelected: false
    };
  }
};
</script>

<style scoped>
.vue-star-rating-star {
  display: inline-block;
}
.vue-star-rating-pointer {
  cursor: pointer;
}
.vue-star-rating {
  /* display: flex; */
  align-items: center;
}
.vue-star-rating-box {
  margin-top: 10px;
  display: flex;
}
.vue-star-rating-text {
  margin-right: 5px;
  margin-left: 5px;
  width: 45px;
  display: inline-block;
}
</style>
