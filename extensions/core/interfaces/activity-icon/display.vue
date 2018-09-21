<template>
  <div v-bind:style="{ color: 'var(--' + color + ')' }">
    <v-progress-ring
      class="icon"
      :radius="17"
      :icon="icon"
      :color="color"
      :progress="100"
      :stroke="2"></v-progress-ring>
      <span class="label">{{label}}</span>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "readonly-activity-icon",
  mixins: [mixin],
  data() {
    return {
      styles: {
        add: {
          icon: "check",
          color: "success"
        },
        update: {
          icon: "check",
          color: "success"
        },
        "soft-delete": {
          icon: "delete",
          color: "warning"
        },
        delete: {
          icon: "delete_forever",
          color: "warning"
        },
        authenticate: {
          icon: "lock_open",
          color: "dark-gray"
        },
        upload: {
          icon: "cloud_done",
          color: "accent"
        },
        mention: {
          icon: "insert_comment",
          color: "purple"
        },
        error: {
          icon: "error",
          color: "danger"
        }
      }
    };
  },
  computed: {
    icon() {
      return this.value && this.styles[this.value.toLowerCase()]
        ? this.styles[this.value.toLowerCase()].icon
        : "help";
    },
    color() {
      return this.value && this.styles[this.value.toLowerCase()]
        ? this.styles[this.value.toLowerCase()].color
        : "lighter-gray";
    },
    label() {
      if (this.value) {

        switch (this.value.toLowerCase()) {
          case "add":
            return "Item Created";
          case "update":
            return "Item Saved";
          case "soft-delete":
            return "Item Deleted";
          case "delete":
            return "Item Deleted";
          case "authenticate":
            return "Authenticated";
          case "upload":
            return "File Uploaded";
          case "mention":
            return "Mentioned";
          case "error":
            return "Error";
        }
      }

      return "Unknown Action";
    }
  }
};
</script>

<style lang="scss" scoped>
.v-progress-ring {
  display: inline-block;
  vertical-align: middle;
}
.label {
  display: inline-block;
  margin-left: 4px;
}
</style>
