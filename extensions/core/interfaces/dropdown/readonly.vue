<template>
  <span class="no-wrap">{{displayValue}}</span>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
    mixins: [mixin],
    computed: {
        displayValue() {
            if (this.options.formatting === "text") {
                const choices =
                    typeof this.options.choices === "string"
                        ? JSON.parse(this.options.choices)
                        : this.options.choices;

                return choices[this.value];
            }

            return this.value;
        }
    }
};
</script>
