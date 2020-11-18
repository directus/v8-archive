<template>
	<div ref="searchFilter" :class="{ open }" class="search-filter">
		<v-header-button
			class="toggle"
			:alert="hasFilters"
			icon="filter_list"
			outline
			@click="open = !open"
		>
			Filter
		</v-header-button>

		<div class="wrapper">
			<v-icon name="search" color="--input-border-color" />
			<input
				ref="searchInput"
				:placeholder="placeholder || $t('search')"
				:value="searchQuery"
				:class="{ 'has-filters': hasFilters }"
				class="search"
				type="text"
				@input="search($event.target.value)"
			/>
			<transition name="fade">
				<button
					v-show="hasFilters"
					:class="{ 'has-filters': hasFilters }"
					class="clear-filters"
					@click="clearFilters"
				>
					<v-icon name="close" />
				</button>
			</transition>
			<button :class="{ 'has-filters': hasFilters }" class="toggle" @click="open = !open">
				<v-icon name="filter_list" />
			</button>
		</div>

		<transition name="slide">
			<div v-show="open" class="dropdown">
				<div class="search field">
					<v-input
						:placeholder="placeholder || $t('search')"
						:value="searchQuery"
						type="search"
						icon-left="search"
						@input="search"
					/>
				</div>

				<div v-for="(filter, i) in filters" :key="i" class="field">
					<invisible-label :html-for="`filter-${i}`">
						{{ fields[filter.field] }} {{ operators[filter.operator] }}
					</invisible-label>
					<div class="name">
						<p class="field-name">{{ fields[filter.field] }}</p>
						<span class="operator-name">
							{{ $t(operators[filter.operator]) }}
							<v-icon name="expand_more" small />
							<select
								:value="filter.operator"
								@change="updateFilter(i, 'operator', $event.target.value)"
							>
								<option
									v-for="(name, operator) in operators"
									:key="operator"
									:value="operator"
								>
									{{ $t(name) }}
								</option>
							</select>
						</span>
						<button class="remove" @click="deleteFilter(i)">
							<v-icon name="delete_outline" />
						</button>
					</div>
					<v-input
						:id="`filter-${i}`"
						autofocus
						:value="filter.value"
						type="text"
						@input="updateFilter(i, 'value', $event)"
					/>
				</div>

				<div class="field">
					<invisible-label html-for="add">
						{{ $t('add_field_filter') }}
					</invisible-label>
					<v-select
						id="add"
						icon="add_circle"
						:placeholder="$t('add_field_filter')"
						:options="fields"
						default-value
						@input="addFilter"
					/>
				</div>
			</div>
		</transition>

		<v-blocker v-if="open" :z-index="18" class="blocker" @click="open = !open" />
	</div>
</template>

<script>
import VBlocker from '../blocker.vue';
import { debounce, cloneDeep } from 'lodash';

export default {
	name: 'SearchFilter',
	components: {
		VBlocker
	},
	props: {
		fieldNames: {
			type: Array,
			default: () => []
		},
		collectionName: {
			type: String,
			default: null
		},
		filters: {
			type: Array,
			default: () => []
		},
		searchQuery: {
			type: String,
			default: ''
		},
		placeholder: {
			type: String,
			default: null
		}
	},
	data() {
		return {
			open: false
		};
	},
	computed: {
		operators() {
			return {
				eq: 'equal_to',
				neq: 'not_equal_to',
				lt: 'less_than',
				lte: 'less_than_equal',
				gt: 'greater_than',
				gte: 'greater_than_equal',
				in: 'in_list',
				nin: 'not_in_list',
				null: 'is_null',
				nnull: 'is_not_null',
				contains: 'contains',
				ncontains: 'not_contains',
				empty: 'is_empty',
				nempty: 'not_empty',
				has: 'related_entries',
				nhas: 'no_related_entries'
			};
		},
		hasFilters() {
			return !!((this.filters && this.filters.length > 0) || this.searchQuery);
		},
		fields() {
			const fields = {};
			this.fieldNames.forEach(name => {
				if (this.collectionName) {
					fields[name] = this.$helpers.formatField(name, this.collectionName);
				} else {
					fields[name] = this.$helpers.formatTitle(name);
				}
			});
			return fields;
		}
	},
	created() {
		this.search = debounce(this.search, 300);
		this.updateFilter = debounce(this.updateFilter, 300);
	},
	mounted() {
		window.addEventListener('click', this.closeFilter);
	},
	beforeDestroy() {
		window.removeEventListener('click', this.closeFilter);
	},
	methods: {
		search(value) {
			this.$emit('search', value);
		},
		addFilter(field) {
			this.$emit('filter', [
				...this.filters,
				{
					field,
					operator: 'contains',
					value: ''
				}
			]);
		},
		updateFilter(index, key, value) {
			const filters = cloneDeep(this.filters);
			filters[index][key] = value;

			this.$emit('filter', filters);
		},
		deleteFilter(index) {
			const filters = cloneDeep(this.filters);
			filters.splice(index, 1);

			this.$emit('filter', filters);
		},
		clearFilters() {
			this.$emit('clear-filters');
			if (this.open) this.open = false;
		},
		closeFilter(event) {
			const contains = this.$refs.searchFilter.contains(event.target);
			if (this.open && contains === false) {
				this.open = false;
			}
		}
	}
};
</script>

<style lang="scss" scoped>
.dropdown {
	position: absolute;
	top: var(--header-height);
	background-color: white;
	width: 100%;
	left: 0;
	z-index: 19;
	padding: 20px;
	color: var(--blue-grey-900);
	transform-origin: top;
	border-bottom: 2px solid var(--input-border-color);
	border-radius: 0 0 var(--border-radius) var(--border-radius);
	background-color: var(--input-background-color-alt);

	@media (min-width: 800px) {
		left: var(--nav-sidebar-width);
		width: calc(100% - var(--nav-sidebar-width));
	}

	@media (min-width: 1000px) {
		border-left: 2px solid var(--input-border-color);
		border-right: 2px solid var(--input-border-color);
		left: 0;
		width: 100%;
	}
}

.blocker {
	top: var(--header-height) !important;

	@media (min-width: 1000px) {
		display: none;
	}
}

.field:not(:last-child) {
	padding-bottom: 20px;

	.name {
		display: flex;
		align-items: center;
		margin-bottom: 4px;
		color: var(--input-placeholder-color);
		font-weight: var(--weight-bold);

		.field-name {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			color: var(--input-text-color);
		}

		.operator-name {
			position: relative;
			margin-left: 8px;
			padding-right: 2em;
			flex-grow: 1;
			display: flex;
			align-items: center;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;

			select {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: +1;
				opacity: 0;
				cursor: pointer;
			}

			i {
				width: 1px; /* Hack to make sure the span text has enough room */
			}
		}

		.remove {
			opacity: 0;
			transition: var(--fast) var(--transition);
			transition-property: color, opacity;
			color: var(--input-text-color);

			i {
				color: var(--input-icon-color);
				margin-left: 8px;
			}

			&:hover,
			.user-is-tabbing &:focus {
				i {
					color: var(--danger);
				}
				opacity: 1;
			}
		}
	}

	&:hover,
	.user-is-tabbing:focus,
	.user-is-tabbing:focus-within {
		.remove {
			opacity: 1;
		}
	}
}

.slide-enter-active {
	transition: var(--medium) var(--transition-in);
}

.slide-leave-active {
	transition: var(--fast) var(--transition-out);
}

.slide-enter,
.slide-leave-to {
	transform: scaleY(0);
	opacity: 0;
}

.wrapper {
	display: none;
}

@media (min-width: 1000px) {
	.search-filter > .toggle {
		display: none !important; /* 😭 */
	}

	.wrapper {
		display: block;
	}

	.dropdown .search.field {
		display: none;
	}

	.search-filter {
		// margin-right: 10px;
		position: relative;

		.search {
			width: 100%;
			height: 44px;
			border-radius: 22px;
			display: block;
			border: 2px solid var(--input-border-color);
			padding: 10px 40px 10px 40px;
			line-height: 1.5;
			transition: var(--fast) var(--transition);
			transition-property: color, border-color, padding, border-radius;
			color: var(--input-text-color);
			background-color: var(--input-background-color);

			&::placeholder {
				color: var(--input-placeholder-color);
			}

			&:hover {
				border-color: var(--input-border-color-hover);
				outline: 0;
			}

			&:focus {
				border-color: var(--input-border-color-focus);
				outline: 0;
			}

			&:focus + i {
				color: var(--blue-grey-800);
			}

			&:-webkit-autofill {
				color: var(--input-text-color) !important;
				-webkit-text-fill-color: var(--input-text-color) !important;
			}

			&:-webkit-autofill,
			&:-webkit-autofill:hover,
			&:-webkit-autofill:focus {
				background-color: var(--white);
				box-shadow: inset 0 0 0 2000px var(--white);
			}

			&.has-filters {
				padding-right: 73px;
			}
		}

		.wrapper {
			position: relative;

			> .v-icon,
			> button {
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				user-select: none;
			}

			> .v-icon {
				left: 10px;
			}

			button .v-icon {
				transition: color var(--fast) var(--transition);
			}

			.toggle {
				transition: all var(--fast) var(--transition);
				right: 10px;
				color: var(--input-border-color);

				&:hover,
				.user-is-tabbing &:focus {
					color: var(--input-text-color);
				}

				&::after {
					content: '';
					display: block;
					width: 8px;
					height: 8px;
					background-color: var(--warning);
					border-radius: 50%;
					position: absolute;
					top: -3%;
					right: -3%;
					border: 2px solid var(--input-background-color);
					transform: scale(0);
					transition: transform var(--fast) var(--transition-out);
				}

				&.has-filters {
					&::after {
						transform: scale(1);
					}
				}
			}

			.clear-filters {
				right: 36px;
				color: var(--input-text-color);

				&:hover,
				.user-is-tabbing &:focus {
					color: var(--danger);
				}
			}
		}

		.dropdown {
			top: auto;
		}

		&.open {
			.toggle {
				i {
					color: var(--input-text-color);
				}
			}

			.search {
				border-top-left-radius: var(--border-radius);
				border-top-right-radius: var(--border-radius);
				border-bottom-left-radius: 0;
				border-bottom-right-radius: 0;
			}
		}
	}
}

@media (min-width: 1000px) {
	.search-filter {
		width: 200px;

		transition: width var(--slow) var(--transition);

		&.open,
		&:focus-within {
			width: 344px;
		}
	}
}
</style>
