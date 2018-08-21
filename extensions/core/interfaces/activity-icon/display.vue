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
        login: {
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
        if (this.value.toLowerCase() == "add") {
          return "Item Created";
        } else if (this.value.toLowerCase() == "update") {
          return "Item Saved";
        } else if (this.value.toLowerCase() == "soft-delete") {
          return "Item Deleted";
        } else if (this.value.toLowerCase() == "delete") {
          return "Item Deleted";
        } else if (this.value.toLowerCase() == "login") {
          return "Authenticated";
        } else if (this.value.toLowerCase() == "upload") {
          return "File Uploaded";
        } else if (this.value.toLowerCase() == "mention") {
          return "Mentioned";
        } else if (this.value.toLowerCase() == "error") {
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
