<docs>
  # Tag Component

  ## Usage

  ```vue
  <v-tag>Title</v-tag>
  ```
</docs>

<template functional>
	<button v-if="props.clickable" v-on="listeners"><slot /></button>
	<span v-else><slot /></span>
</template>

<script>
export default {
	name: 'VTag',
	props: {
		clickable: {
			type: Boolean,
			default: false
		}
	}
};
</script>

<style scoped>
button,
span {
	transition: all var(--fast) var(--transition);
	margin: 4px 8px 4px 0;
	padding: 4px 8px 5px;
	background-color: var(--input-background-color-alt);
	border-radius: var(--border-radius);
}

button:hover {
	background-color: var(--danger);
	color: var(--white);
}
</style>
