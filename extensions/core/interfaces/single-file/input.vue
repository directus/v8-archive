<template>
  <div class="input-single-file">
    <v-card
      v-if="value"
      class="card"
      :title="value.title"
      :subtitle="value.type"
      :src="value.data.full_url"
      color="accent" />

    <button class="style-btn" type="button" @click="newFile = true">
      <i class="material-icons">add</i>{{ $t('new') }}
    </button>

    <button class="style-btn" type="button" @click="existing = true">
      <i class="material-icons">playlist_add</i>{{ $t('select_existing') }}
    </button>

    <portal to="modal" v-if="newFile">
      <v-modal :title="$t('file_upload')" @close="newFile = false">
        <v-upload />
      </v-modal>
    </portal>

    <portal to="modal" v-if="existing">
      <v-modal :title="$t('choose_one')" @close="existing = false">
        <v-items
          collection="directus_files"
          :selection="selection"
          :filters="{}"
          :view-query="{}"
          :view-type="{}"
          :view-options="{}"
          @options="() => {}"
          @query="() => {}"
          @select="selection = $event[$event.length - 1]" />
      </v-modal>
    </portal>
  </div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  mixins: [mixin],
  data() {
    return {
      newFile: false,
      existing: false,
      selection: []
    };
  }
};
</script>

<style lang="scss" scoped>
.card {
  margin-bottom: 20px;
}

button {
  background-color: var(--accent);
  border-radius: var(--border-radius);
  height: var(--input-height);
  padding: 0 10px;
  display: inline-flex;
  align-items: center;
  margin-right: 10px;
  transition: background-color var(--fast) var(--transition);

  i {
    margin-right: 5px;
  }

  &:hover {
    transition: none;
    background-color: var(--accent-dark);
  }
}
</style>
