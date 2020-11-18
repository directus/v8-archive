<template>
	<div class="project-switcher">
		<div
			v-tooltip.left="{
				content: tooltipContent,
				boundariesElement: 'body'
			}"
			class="content"
			:class="{
				slow: signalStrength === 1,
				disconnected: signalStrength === 0
			}"
		>
			<v-signal class="icon" />
			<span class="no-wrap project-name">
				<template v-if="currentProject.status === 'successful'">
					{{ currentProject.data.project_name }}
				</template>
				<template v-else>
					{{ currentProjectKey }}
				</template>
			</span>
			<v-icon v-if="projects.length > 1" class="chevron" name="expand_more" />
			<select v-if="projects.length > 1" v-model="currentProjectKey">
				<option
					v-for="project in projects"
					:key="project.key"
					:name="project.key"
					:value="project.key"
					:selected="currentProjectKey === project.key"
				>
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
	</div>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
import VSignal from '../../signal.vue';

export default {
	name: 'ProjectSwitcher',
	components: {
		VSignal
	},
	computed: {
		...mapState(['projects', 'apiRootPath', 'latency']),
		...mapGetters(['currentProject', 'signalStrength']),
		currentProjectKey: {
			get() {
				return this.$store.state.currentProjectKey;
			},
			set(value) {
				this.$store.dispatch('setCurrentProject', value);
			}
		},
		apiURL() {
			return window.location.origin + this.apiRootPath + this.currentProjectKey;
		},
		tooltipContent() {
			let latency = this.latency[this.latency.length - 1].latency;

			latency = Math.round(latency);

			if (latency) {
				latency = this.$n(latency);
			}

			let content = this.apiURL;
			content += '<br>';
			content += this.$t('latency') + ':';
			content += ` ${latency}ms`;

			return content;
		}
	}
};
</script>

<style lang="scss" scoped>
.project-switcher > div {
	height: var(--header-height);
	width: calc(100% + 24px);
	display: flex;
	align-items: center;
	margin: 0 -12px 12px;
	padding: 0 22px;
	position: relative;
	background-color: var(--sidebar-background-color-alt);

	&:hover {
		.chevron {
			opacity: 1;
		}
	}

	.content {
		padding: 8px 0 8px 10px;
	}

	&.slow {
		svg {
			transition: color 0.25s ease-in-out, fill 0.25s ease-in-out;
		}

		&.slow {
			svg {
				fill: var(--warning);
			}
		}

		&.disconnected {
			svg {
				fill: var(--danger);
			}
		}

		svg {
			fill: var(--sidebar-text-color);
		}

		i {
			color: var(--sidebar-text-color);
		}
	}

	.icon {
		flex-shrink: 0;
		width: 21px;
		height: 24px;
		margin-right: 21px;
		color: var(--sidebar-text-color);
		fill: var(--sidebar-text-color);
	}

	.project-name {
		padding-right: 12px;
	}

	.chevron {
		position: absolute;
		right: 10px;
		color: var(--sidebar-text-color);
		opacity: 0;
		transition: all var(--fast) var(--transition);
	}

	select {
		position: absolute;
		opacity: 0;
		left: 0;
		top: 50%;
		transform: translateY(-50%);
		width: 100%;
		height: 100%;
		cursor: pointer;
	}
}
</style>
