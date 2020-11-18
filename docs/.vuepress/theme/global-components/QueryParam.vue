<template>
	<Content :page-key="pageKey" :slot-key="param" />
</template>

<script>
	export default {
		props: {
			param: {
				type: String,
				required: true
			}
		},
		computed: {
			pageKey() {
				return this.$site.pages.find(page => {
					return page.path === "/api/params/inline.html";
				}).key;
			}
		}
	}
</script>