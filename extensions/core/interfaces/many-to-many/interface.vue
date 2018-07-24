<template>
  <div class="interface-many-to-many">
    <div class="table">
      <div class="header">
        <div class="row">
          <div v-for="column in columns" :key="column.field">{{ column.name }}</div>
        </div>
      </div>
      <div class="body">
        <div
          v-for="item in items"
          class="row"
          :key="item.id"
          @click="editItem(item)">
          <div
            v-for="column in columns"
            :key="column.field">{{ item[column.field] }}</div>
          <button
            type="button"
            class="remove-item"
            @click.stop="warnRemoveitem(item.id)">
            <i class="material-icons">close</i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import mixin from "../../../mixins/interface";

  export default {
    mixins: [mixin],
    name: "interface-many-to-many",
    computed: {
      items() {
        return this.value.map(val => val.movie);
      },
      columns() {
        if (!this.options.fields) return null;
        const fields = this.options.fields.split(",").map(val => val.trim());

        return fields.map(field => ({
          field,
          name: this.$helpers.formatTitle(field)
        }));
      }
    }
  }
</script>

<style lang="scss" scoped>
.table {
  background-color: var(--white);
  border: var(--input-border-width) solid var(--lighter-gray);
  border-radius: var(--border-radius);
  border-spacing: 0;
  width: 100%;
  margin: 10px 0 20px;

  .header {
    height: var(--input-height);
    color: var(--gray);
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 700;
    border-bottom: 1px solid var(--lighter-gray);
  }

  .row {
    display: flex;
    align-items: center;
    padding: 0 5px;

    > div {
      padding: 3px 5px;
      flex-basis: 200px;
    }
  }

  .header .row {
    align-items: center;
    height: 40px;
  }

  .body {
    max-height: 275px;
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;

    .row {
      cursor: pointer;
      position: relative;
      height: 50px;
      border-bottom: 1px solid var(--lightest-gray);

      &:hover {
        background-color: var(--highlight);
      }

      & div:last-of-type {
        flex-grow: 1;
      }

      button {
        color: var(--lighter-gray);
        transition: color var(--fast) var(--transition);

        &:hover {
          transition: none;
          color: var(--danger);
        }
      }
    }
  }
}
</style>
