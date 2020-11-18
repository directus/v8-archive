<template>
	<div v-if="userInfo" class="user-updated">
		<v-avatar
			v-if="options.display !== 'name'"
			v-tooltip="options.display === 'avatar' ? displayValue : null"
			class="avatar"
			x-small
		>
			<img v-if="src" :src="src" />
			<v-icon name="person" small v-else />
		</v-avatar>
		<span v-if="options.display !== 'avatar'" class="name">
			{{ displayValue }}
		</span>
		<v-icon name="account_box" color="--input-icon-color" />
	</div>
	<v-input
		v-else-if="newItem"
		:readonly="true"
		:placeholder="$t('interfaces.user.updated-you')"
		icon-right="account_box"
	/>
	<v-input
		v-else
		:readonly="true"
		:placeholder="$t('interfaces.user-updated.unknown')"
		icon-right="account_box"
	/>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],
	computed: {
		userInfo() {
			if (!this.value) return null;
			if (typeof this.value === 'object') {
				if (this.value.first_name) {
					return this.value;
				} else {
					return this.$store.state.users[this.value.id];
				}
			}
			return this.$store.state.users[this.value];
		},
		displayValue() {
			return this.$helpers.micromustache.render(this.options.template, this.userInfo);
		},
		src() {
			if (!this.userInfo.avatar) return null;
			return this.userInfo.avatar.data.thumbnails[0].url;
		}
	}
};
</script>

<style lang="scss" scoped>
.user-updated {
	position: relative;
	border: var(--input-border-width) solid var(--input-border-color);
	border-radius: var(--border-radius);
	height: var(--input-height);
	font-size: var(--input-font-size);
	padding: 4px 10px;
	display: flex;
	align-items: center;
	background-color: var(--input-background-color-disabled);

	> .v-icon {
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		right: 10px;
	}
}
.avatar {
	display: inline-flex;
	margin-right: 12px;
}
.name {
	display: inline-block;
}
</style>
