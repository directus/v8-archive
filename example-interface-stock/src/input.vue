<template>
  <div>
    <input
      class="input"
      type="text"
      :value="value"
      :readonly="readonly"
      :placeholder="$t('interfaces-stock-placeholder_text')"
      @input="$emit('input', $event.target.value)"
    />
    <div
      :class="[
        change < 0 ? 'down' : 'up',
        change == 0 ? '' : 'colorize',
        'quote'
      ]"
    >
      <span class="latest">{{ latestPrice }}</span
      ><span class="currency">USD</span>
      <div class="change-container">
        <span class="change">{{ change }}<br />{{ changePercent }}</span>
        <i class="material-icons trending">{{ trending }}</i>
      </div>
    </div>
    <div :class="[showChart ? '' : 'hidden', 'data-container']">
      <div v-if="companyName" class="name">
        {{ companyName }} <span>({{ primaryExchange }})</span>
      </div>
      <div class="history-container">
        <canvas id="history" height="120"></canvas>
      </div>
      <div class="details">
        <span
          ><b>{{ $t("interfaces-stock-details_open") }}:</b>
          {{ details.open }}</span
        >
        <span
          ><b>{{ $t("interfaces-stock-details_mktcap") }}:</b>
          {{ details.marketCap }}</span
        >
        <span
          ><b>{{ $t("interfaces-stock-details_high") }}:</b>
          {{ details.high }}</span
        >
        <span
          ><b>{{ $t("interfaces-stock-details_52whigh") }}:</b>
          {{ details.week52High }}</span
        >
        <span
          ><b>{{ $t("interfaces-stock-details_low") }}:</b>
          {{ details.low }}</span
        >
        <span
          ><b>{{ $t("interfaces-stock-details_52wlow") }}:</b>
          {{ details.week52Low }}</span
        >
      </div>
    </div>
    <div v-if="message" class="message">{{ message }}</div>
  </div>
</template>

<script>
import Chart from "chart.js";
import hexToRgba from "hex-to-rgba";
import NumAbbr from "number-abbreviate";
import mixin from "@directus/mixins/interface";

function getCSS(name) {
  return getComputedStyle(document.documentElement)
    .getPropertyValue(name)
    .trim();
}

export default {
  mixins: [mixin],
  data() {
    return {
      details: {
        open: 0,
        marketCap: 0,
        high: 0,
        low: 0,
        week52High: 0,
        week52Low: 0
      },
      latestPrice: "0.00",
      change: "0.00",
      changePercent: "0.00%",
      trending: "trending_flat",
      companyName: "",
      primaryExchange: "",
      message: "Please enter a stock symbol",
      showChart: false,
      historyData: {
        labels: [1, 2],
        datasets: [
          {
            backgroundColor: hexToRgba(getCSS("--darkest-gray"), 0.2),
            borderColor: hexToRgba(getCSS("--darkest-gray"), 1.0),
            pointRadius: 0,
            borderWidth: 1,
            data: [1, 2]
          }
        ]
      },
      historyOptions: {
        responsive: true,
        maintainAspectRatio: false,
        showTooltips: true,
        lineTension: 1,
        tooltips: {
          mode: "index",
          intersect: false,
          titleFontFamily: "Roboto",
          bodyFontFamily: "Roboto",
          backgroundColor: hexToRgba(getCSS("--white"), 0.8),
          titleFontColor: hexToRgba(getCSS("--dark-gray"), 1.0),
          bodyFontColor: hexToRgba(getCSS("--dark-gray"), 1.0),
          footerFontColor: hexToRgba(getCSS("--light-gray"), 1.0),
          borderColor: hexToRgba(getCSS("--lighter-gray"), 1.0),
          xPadding: 10,
          yPadding: 6,
          titleSpacing: 0,
          titleMarginBottom: 2,
          bodySpacing: 6,
          caretPadding: 6,
          cornerRadius: 3,
          borderWidth: 2,
          displayColors: false
        },
        scales: {
          xAxes: [
            {
              gridLines: {
                display: false
              },
              ticks: {
                display: false,
                autoSkip: true,
                maxTicksLimit: 3,
                maxRotation: 0
              }
            }
          ],
          yAxes: [
            {
              gridLines: {
                color: hexToRgba(getCSS("--lightest-gray"), 1.0),
                drawTicks: false,
                drawBorder: false
              },
              ticks: {
                showLabelBackdrop: true,
                backdropColor: hexToRgba(getCSS("--lighter-gray"), 1.0),
                fontColor: hexToRgba(getCSS("--lighter-gray"), 1.0),
                fontFamily: "Roboto",
                fontSize: 12,
                autoSkip: true,
                maxTicksLimit: 3,
                mirror: false,
                padding: 8
              }
            }
          ]
        },
        legend: {
          display: false
        }
      }
    };
  },
  watch: {
    value(newValue, oldValue) {
      if (newValue) {
        this.message = "Processing...";
        this.getStock();
      } else {
        this.message = "Please enter a stock symbol...";
        this.showChart = false;
        this.latestPrice = "0.00";
        this.change = "0.00";
        this.changePercent = "0.00%";
        this.trending = "trending_flat";
      }
    }
  },
  created() {
    this.getStock = this.$lodash.debounce(this.getStock, 1000);

    if (this.value) {
      this.message = "Processing...";
      this.getStock();
    }
  },
  methods: {
    getStock() {
      const symbol = this.value;
      const numAbbr = new NumAbbr();

      this.$api.axios
        .get(
          "https://api.iextrading.com/1.0/stock/" +
            symbol +
            "/batch?types=quote,chart&range=dynamic&chartLast=100"
        )
        .then(response => {
          this.showChart = true;
          this.message = "";

          this.companyName = response.data.quote.companyName;
          this.primaryExchange = response.data.quote.primaryExchange;
          this.latestPrice = response.data.quote.latestPrice.toFixed(2);

          this.details.open = response.data.quote.open.toFixed(2);
          this.details.marketCap = numAbbr
            .abbreviate(response.data.quote.marketCap, 2)
            .toUpperCase();
          this.details.high = response.data.quote.high.toFixed(2);
          this.details.low = response.data.quote.low.toFixed(2);
          this.details.week52High = response.data.quote.week52High.toFixed(2);
          this.details.week52Low = response.data.quote.week52Low.toFixed(2);

          const dataLabels = [];
          const dataValues = [];

          for (var key in response.data.chart.data) {
            if (response.data.chart.data.hasOwnProperty(key)) {
              dataLabels[key] = response.data.chart.data[key].date;
              dataValues[key] = response.data.chart.data[key].close;
            }
          }

          this.historyData.labels = dataLabels;
          this.historyData.datasets[0].data = dataValues;

          if (response.data.quote.change > 0) {
            this.trending = "trending_up";
            this.change = "+" + this.$lodash.round(response.data.quote.change, 2);
            this.changePercent =
              "+" +
              this.$lodash.round(response.data.quote.changePercent * 100, 2) +
              "%";
            this.historyData.datasets[0].backgroundColor = hexToRgba(
              getCSS("--green"),
              0.2
            );
            this.historyData.datasets[0].borderColor = hexToRgba(
              getCSS("--green"),
              1.0
            );
          } else {
            this.trending = "trending_down";
            this.change = this.$lodash.round(response.data.quote.change, 2);
            this.changePercent =
              this.$lodash.round(response.data.quote.changePercent * 100, 2) +
              "%";
            this.historyData.datasets[0].backgroundColor = hexToRgba(
              getCSS("--red"),
              0.2
            );
            this.historyData.datasets[0].borderColor = hexToRgba(
              getCSS("--red"),
              1.0
            );
          }

          // Generate History Chart
          var ctx = document.getElementById("history");

          if (this.chart) {
            this.chart.destroy();
            this.chart = null;
          }

          this.chart = new Chart(ctx, {
            type: "line",
            data: this.historyData,
            options: this.historyOptions
          });
        })
        .catch(error => {
          this.message = "Invalid stock symbol";
          this.showChart = false;
          this.latestPrice = "0.00";
          this.change = "0.00";
          this.changePercent = "0.00%";
          this.trending = "trending_flat";
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.input {
  border: var(--input-border-width) solid var(--lighter-gray);
  height: var(--input-height);
  border-radius: var(--border-radius);
  max-width: var(--width-x-small);
  display: inline-block;
  text-transform: uppercase;
  &:read-only {
    background-color: var(--lightest-gray);
    cursor: not-allowed;
  }
}
.quote {
  display: inline-block;
  margin-left: 10px;
  vertical-align: middle;
  &.up.colorize .change-container {
    background-color: var(--green);
  }
  &.down.colorize .change-container {
    background-color: var(--red);
  }
  &.colorize .latest {
    color: var(--darkest-gray);
  }
  .latest {
    color: var(--lighter-gray);
    font-weight: 300;
    font-size: 32px;
    line-height: 28px;
  }
  .currency {
    font-weight: 600;
    color: var(--lighter-gray);
    font-size: 10px;
    line-height: 28px;
  }
  .change-container {
    display: inline-block;
    background-color: var(--lighter-gray);
    border-radius: var(--border-radius);
    padding: 7px 10px;
    height: 40px;
    color: var(--white);
    margin-left: 10px;
    margin-top: -3px;
    .change {
      display: inline-block;
      line-height: 13px;
    }
    .trending {
      transform: translateY(-33%);
      margin-left: 4px;
    }
  }
}
.data-container {
  background: var(--white);
  box-shadow: var(--box-shadow);
  border-radius: var(--border-radius);
  padding: 20px;
  margin-top: 20px;
  max-width: var(--width-large);
  opacity: 1;
  &.hidden {
    margin-top: 0;
    padding: 0;
    height: 0;
    opacity: 0;
  }
  .history-container {
    height: 120px;
    margin-left: -6px;
    overflow: hidden;
  }
  .details {
    color: var(--light-gray);
    margin-top: 14px;
    display: grid;
    grid-template-columns: 50% 50%;
    grid-gap: 10px;
    row-gap: 4px;
    font-weight: 400;
    span {
      padding-right: 10px;
      b {
        color: var(--gray);
        font-weight: 600;
        width: 40px;
        width: 80px;
        display: inline-block;
      }
    }
  }
}
.name {
  font-size: 18px;
  line-height: 21px;
  font-weight: 400;
  margin-bottom: 16px;
  span {
    color: var(--lighter-gray);
  }
}
.message {
  margin-top: 10px;
}
</style>
