<template>
	<div v-if="projects && projects.length > 1" class="project-chooser">
		<span v-tooltip.right="{ classes: ['inverted'], content: 'API URL' }" class="preview">
			<v-signal class="signal" />
			<template v-if="currentProject.status === 'successful'">
				{{ currentProject.data.project_name }}
			</template>
			<template v-else>
				{{ currentProjectKey }}
			</template>
			<v-icon class="icon dropdown" color="--input-text-color" name="arrow_drop_down" />
		</span>
		<select v-model="currentProjectKey">
			<option v-for="project in projects" :key="project.key" :value="project.key">
				<template v-if="project.status === 'successful'">
					{{ project.data.project_name }}
					<template v-if="project.data.authenticated === true">
						&#8226;
					</template>
				</template>
				<template v-else>
					{{ project.key }}
				</template>
			</option>
		</select>
	</div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import VSignal from '@/components/signal';

export default {
	name: 'ProjectChooser',
	components: {
		VSignal
	},
	computed: {
		...mapGetters(['currentProject']),
		...mapState(['projects']),
		currentProjectKey: {
			get() {
				return this.$store.state.currentProjectKey;
			},
			set(value) {
				this.$store.dispatch('setCurrentProject', value);
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.project-chooser {
	border: 2px solid var(--input-border-color);
	width: 100%;
	padding: 20px 10px;
	margin-bottom: 32px;
	color: var(--input-text-color);
	transition: border-color var(--fast) var(--transition);
	border-radius: var(--border-radius);
	position: relative;

	&:hover {
		border-color: var(--input-border-color-hover);
	}

	&:focus {
		border-color: var(--input-border-color-focus);
	}

	.preview {
		display: inline-block;
		position: relative;
		font-size: 16px;
		transition: color var(--fast) var(--transition);
		color: var(--input-text-color);
		width: 100%;
		padding: 0 36px;

		.signal {
			position: absolute;
			left: 2px;
			top: calc(50% - 12px);
			user-select: none;
			pointer-events: none;
			width: 24px;
			height: 24px;
			color: var(--input-text-color);
			fill: var(--input-text-color);
		}

		.dropdown {
			position: absolute;
			right: 0;
			top: calc(50% - 13px);
			user-select: none;
			pointer-events: none;
		}
	}

	select {
		position: absolute;
		width: 100%;
		height: 100%;
		opacity: 0;
		z-index: +1;
		top: 0;
		left: 0;
		font-size: 16px;
		cursor: pointer;
		appearance: none;
	}

	select:hover + div,
	select:focus + div {
		transition: none;
		cursor: pointer;
	}
}
</style>
