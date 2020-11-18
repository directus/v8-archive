<template>
  <div
    v-if="value"
    :class="[
      change < 0 ? 'down' : 'up',
      change == 0 ? '' : 'colorize',
      'quote'
    ]"
  >
    <div class="stock-container" :title="companyName">
      <span class="symbol">{{ displayValue }}:</span
      ><span class="latest">{{ latestPrice }}</span
      ><span class="currency">USD</span>
    </div>
    <div
      class="change-container"
      @click.prevent="swapUnits()"
      :title="$t('interfaces-stock-swap_units')"
    >
      <span v-if="showUnit" class="change units">{{ change }}</span
      ><span v-else class="change percent">{{ changePercent }}</span>
      <i class="material-icons trending">{{ trending }}</i>
    </div>
  </div>
  <div v-else class="empty">--</div>
</template>

<script>
import mixin from "@directus/mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      companyName: "",
      primaryExchange: "",
      latestPrice: "0.00",
      change: "0.00",
      changePercent: "0.00%",
      trending: "trending_flat",
      showUnit: true
    };
  },
  methods: {
    swapUnits(event) {
      this.showUnit = this.showUnit ? false : true;
    }
  },
  computed: {
    displayValue() {
      let vm = this;
      let symbol = this.value;

      this.$api.axios
        .get(
          "https://api.iextrading.com/1.0/stock/" +
            symbol +
            "/batch?types=quote"
        )
        .then(function(response) {
          vm.companyName =
            response.data.quote.companyName +
            " (" +
            response.data.quote.primaryExchange +
            ")";
          vm.latestPrice = response.data.quote.latestPrice.toFixed(2);

          if (response.data.quote.change > 0) {
            vm.trending = "trending_up";
            vm.change = "+" + vm.$lodash.round(response.data.quote.change, 2);
            vm.changePercent =
              "+" +
              vm.$lodash.round(response.data.quote.changePercent * 100, 2) +
              "%";
          } else {
            vm.trending = "trending_down";
            vm.change = vm.$lodash.round(response.data.quote.change, 2);
            vm.changePercent =
              vm.$lodash.round(response.data.quote.changePercent * 100, 2) +
              "%";
          }
        })
        .catch(function(error) {
          vm.latestPrice = "0.00";
          vm.change = "0.00";
          vm.changePercent = "0.00%";
          vm.trending = "trending_flat";
          console.error("Error:", error);
        });

      return symbol;
    }
  }
};
</script>

<style lang="scss" scoped>
.quote {
  display: inline-block;
  &.up.colorize .change-container {
    background-color: var(--green);
  }
  &.down.colorize .change-container {
    background-color: var(--red);
  }
  &.colorize .stock-container .latest {
    color: var(--darkest-gray);
  }
  .stock-container {
    display: inline-block;
    cursor: help;

    span {
      display: inline-block;
      vertical-align: middle;
      &:not(:last-of-type) {
        margin-right: 4px;
      }
    }

    .symbol {
      display: inline-block;
      font-weight: 400;
      font-size: 18px;
      text-transform: uppercase;
      color: var(--lighter-gray);
    }
    .latest {
      color: var(--lighter-gray);
      font-weight: 400;
      font-size: 18px;
      margin-left: 1px;
    }
    .currency {
      color: var(--lighter-gray);
      font-weight: 600;
      font-size: 10px;
      line-height: 28px;
      margin-left: 1px;
    }
  }
  .change-container {
    position: relative;
    display: inline-block;
    background-color: var(--lighter-gray);
    border-radius: var(--border-radius);
    padding: 0px 6px;
    color: var(--white);
    margin-left: 10px;
    cursor: pointer;
    &:hover {
      opacity: 0.8;
    }
    .change {
      display: inline-block;
      vertical-align: middle;
    }
  }
}
</style>
