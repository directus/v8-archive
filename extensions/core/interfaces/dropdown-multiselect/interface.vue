<template>
  <select
    :disabled="readonly"
    class="select"
    :class="width"
    @change="updateValue($event.target.options)"
    :id="name"
    multiple
  >
    <option
      v-if="options.placeholder"
      value=""
      :disabled="required"
    >{{options.placeholder}}</option>
    <option
      v-for="(display, val) in options.choices"
      :value="val"
      :selected="value && value.includes(val)"
    >{{display}}</option>
  </select>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    mixins: [mixin],
    computed: {
        choices() {
            let choices = this.options.choices;

            if (!choices) return {};

            if (typeof this.options.choices === "string") {
                choices = JSON.parse(this.options.choices);
            }

            return choices;
        },
        width() {
            if (!this.choices) return "medium";

            let longestValue = "";
            Object.values(this.choices).forEach(choice => {
                if (choice.length > longestValue.length) {
                    longestValue = choice;
                }
            });

            const length = longestValue.length;

            if (length <= 7) return "x-small";
            else if (length > 7 && length <= 25) return "small";
            else return "medium";
        }
    },
    methods: {
        updateValue(options) {
            let value = Array.from(options)
                .filter(input => input.selected && Boolean(input.value))
                .map(input => input.value)
                .join();

            if (value && this.options.wrapWithDelimiter) {
                value = `,${value},`;
            }

            this.$emit("input", value);
        }
    }
};
</script>

<style lang="scss" scoped>
.select {
    border: var(--input-border-width) solid var(--light-gray);
    border-radius: var(--border-radius);
    width: 100%;
    font-family: "Roboto", sans-serif;

    &:focus {
        border-color: var(--accent);
        option {
            color: var(--darker-gray);
        }
    }
    option {
        padding: 5px 10px;
        color: var(--gray);

        &:checked {
            background: var(--accent)
                linear-gradient(0deg, var(--accent) 0%, var(--accent) 100%);
            position: relative;
            color: var(--white);
            -webkit-text-fill-color: var(--white);

            &::after {
                content: "check";
                font-family: "Material Icons";
                font-size: 24px;
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-54%);
            }
        }
    }
}

.x-small {
    max-width: var(--width-x-small);
}

.small {
    max-width: var(--width-small);
}

.medium {
    max-width: var(--width-normal);
}
</style>
