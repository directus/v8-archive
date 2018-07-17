<template>
    <div class="interface-icons">
      <h1>Todo</h1>
      <p>willie wartaal</p>
    </div>
</template>

<script>
import mixin from "../../../mixins/interface";
/*
@REQUIREMENTS
It should show every icon in the material icon set that is available for the user.
The user should search through the icons and when the icon is set, it will be obviously know to the user that the icon is set (active).

@TODO
- create a scraper for all the material design icons.
- make an object with all the material design names with value.
- The object should be created within the group. So every group has it's icons.
- make an array that has every material design icon in the application.
- loop through the object and fill the array with the values.
- use .filter() and .contains() the user input variable.
- Show every material
*/

export default {
  mixins: [mixin],
  computed: {}
};
</script>

<style lang="scss" scoped>
</style>

