<template>
  <v-select
    icon="perm_identity"
    :id="name"
    :name="name"
    :placeholder="$t('choose_one')"
    :options="selectOptions"
    :value="currentRoleID"
    @input="emitValue"
  ></v-select>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  name: "interface-user-role",
  data() {
    return {
      loading: false,
      roles: [],
      error: null
    };
  },
  computed: {
    selectOptions() {
      const options = {};

      this.roles
        .filter(role => role.id !== 2) // 2 = public role
        .forEach(role => {
          options[role.id] = role.name;
        });

      return options;
    },
    currentRole() {
      const value = this.value && this.value[0] && this.value[0].role;

      if (typeof value !== "object") {
        const role = this.roles.filter(role => role.id == value);
        return role && role[0];
      }

      return value;
    },
    currentRoleID() {
      return this.currentRole && this.currentRole.id;
    }
  },
  created() {
    this.fetchRoles();
  },
  methods: {
    fetchRoles() {
      this.loading = true;

      this.$api
        .getRoles()
        .then(res => res.data)
        .then(roles => {
          this.roles = roles;
          this.loading = false;
          this.error = null;
        })
        .catch(error => {
          this.loading = false;
          this.error = error;
        });
    },
    emitValue(value) {
      const currentJunctionRecordID =
        this.value && this.value[0] && this.value[0].id;

      if (currentJunctionRecordID) {
        this.$emit("input", [
          {
            id: currentJunctionRecordID,
            role: value
          }
        ]);
      } else {
        this.$emit("input", [
          {
            role: value
          }
        ]);
      }
    }
  }
};
</script>

<style lang="scss" scoped></style>
