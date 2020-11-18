<template>
	<v-not-found v-if="exists === false" />
	<v-ext-module v-else :id="id" />
</template>

<script>
import store from '../store/';
import VNotFound from './not-found.vue';

export default {
	name: 'ModuleExtension',
	metaInfo() {
		const module = this.$store.state.extensions.modules[this.id];

		if (!module) return null;

		return {
			title: module.name
		};
	},
	components: {
		VNotFound
	},
	props: {
		id: {
			type: String,
			required: true
		}
	},
	data() {
		return {
			exists: false
		};
	},
	beforeRouteEnter(to, from, next) {
		const modules = store.state.extensions.modules;
		const id = to.params.id;

		let exists = false;

		if (modules.hasOwnProperty(id)) {
			exists = true;
		}

		return next(vm => {
			vm.exists = exists;
		});
	},
	beforeRouteUpdate(to, from, next) {
		const modules = this.$store.state.extensions.modules;
		const id = this.id;

		this.exists = false;

		if (modules.hasOwnProperty(id)) {
			this.exists = true;
		}

		return next();
	}
};
</script>
