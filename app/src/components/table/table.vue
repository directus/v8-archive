<template>
	<div
		ref="container"
		:style="{ minWidth: totalWidth + 'px' }"
		class="v-table"
		:class="{ loading }"
		@scroll="onScroll"
	>
		<div class="toolbar" :class="{ shadow: scrolled }">
			<div v-if="manualSortField" class="manual-sort cell" :class="{ active: manualSorting }">
				<button v-tooltip="$t('enable_manual_sorting')" @click="startManualSorting">
					<v-icon name="sort" />
				</button>
			</div>
			<div v-if="selectable" class="select cell">
				<v-checkbox
					id="select-all"
					:inputValue="allSelected"
					name="select-all"
					value="all"
					@change="selectAll"
				/>
			</div>
			<div
				v-for="({ field, name }, index) in columns"
				:key="field"
				:style="{
					flexBasis: widths && widths[field] ? widths[field] + 'px' : null
				}"
				class="cell"
			>
				<button
					v-if="sortable && !isRelational(columns[index].fieldInfo)"
					:class="{ active: sortVal.field === field }"
					class="sort type-table-head no-wrap"
					@click="updateSort(field)"
				>
					{{
						widths[field] > 40
							? $helpers.formatField(field, columns[index].fieldInfo.collection)
							: null
					}}
					<v-icon
						class="sort-icon"
						color="--input-border-color-hover"
						name="sort"
						:class="sortVal.asc ? 'asc' : 'desc'"
					/>
				</button>

				<span
					v-else
					v-tooltip="
						isRelational(columns[index].fieldInfo)
							? $t('cant_sort_by_this_field')
							: undefined
					"
					class="type-table-head"
				>
					{{ widths[field] > 40 ? name : null }}
				</span>

				<div
					v-if="resizeable && index !== columns.length - 1"
					class="drag-handle"
					draggable
					@drag="drag(field, $event)"
					@dragstart="hideDragImage"
					@dragend="dragEnd"
				>
					<div class="drag-handle-line" />
				</div>
			</div>
		</div>
		<div class="body" :class="{ loading, dragging }">
			<div v-if="loading && items.length === 0" class="loader">
				<div v-for="n in 50" :key="n" class="row" :style="{ height: rowHeight + 'px' }" />
			</div>
			<component
				:is="manualSorting ? 'draggable' : 'div'"
				v-model="itemsManuallySorted"
				:options="{ handle: '.manual-sort' }"
				@start="startSort"
				@end="saveSort"
			>
				<template v-if="link">
					<div
						v-for="row in itemsArray"
						:key="row[primaryKeyField]"
						:style="{ height: rowHeight + 'px' }"
						:class="{
							selected: selection && selection.includes(row[primaryKeyField])
						}"
						class="link row"
						tabindex="0"
						role="link"
						@click.stop="$router.push(row[link])"
						@keyup.enter.stop="$router.push(row[link])"
					>
						<div
							v-if="manualSortField"
							class="manual-sort cell"
							:class="{ active: manualSorting }"
							@click.stop.prevent
						>
							<v-icon name="drag_handle" />
						</div>
						<div v-if="selectable" class="cell select" @click.stop>
							<v-checkbox
								:id="'check-' + row[primaryKeyField]"
								:value="`${row[primaryKeyField]}`"
								:inputValue="selection.includes(row[primaryKeyField])"
								@change="toggleCheckbox(row[primaryKeyField])"
							/>
						</div>
						<div
							v-for="{ field, fieldInfo } in columns"
							:key="field"
							:style="{
								flexBasis: widths && widths[field] ? widths[field] + 'px' : null
							}"
							class="cell"
						>
							<div
								v-if="
									(row[field] === '' || isNil(row[field])) &&
										fieldInfo &&
										fieldInfo.type.toLowerCase() !== 'alias'
								"
								class="empty"
							>
								--
							</div>
							<v-ext-display
								v-else-if="
									useInterfaces &&
										(!isNil(row[field]) ||
											(fieldInfo && fieldInfo.type.toLowerCase() === 'alias'))
								"
								:id="field"
								:interface-type="fieldInfo.interface"
								:name="field"
								:values="row"
								:collection="collection"
								:type="fieldInfo.type"
								:datatype="fieldInfo.datatype"
								:options="fieldInfo.options"
								:value="row[field]"
								:relation="fieldInfo.relation"
								class="ellipsis"
							/>
							<template v-else>
								{{ row[field] }}
							</template>
						</div>
					</div>
				</template>

				<template v-else>
					<div
						v-for="row in itemsArray"
						:key="row[primaryKeyField]"
						:style="{ height: rowHeight + 'px' }"
						class="row"
					>
						<div v-if="selectable" class="select" @click.stop>
							<v-checkbox
								:id="'check-' + row[primaryKeyField]"
								:value="`${row[primaryKeyField]}`"
								:inputValue="selection.includes(row[primaryKeyField])"
								@change="toggleCheckbox(row[primaryKeyField])"
							/>
						</div>
						<div
							v-for="{ field, fieldInfo } in columns"
							:key="field"
							:style="{
								flexBasis: widths && widths[field] ? widths[field] + 'px' : null
							}"
							class="cell"
						>
							<div v-if="row[field] === '' || isNil(row[field])" class="empty">
								--
							</div>
							<v-ext-display
								v-else-if="useInterfaces && !isNil(row[field])"
								:id="field"
								:interface-type="fieldInfo.interface"
								:name="field"
								:collection="collection"
								:type="fieldInfo.type"
								:options="fieldInfo.options"
								:value="row[field]"
							/>
							<template v-else>
								{{ row[field] }}
							</template>
						</div>
					</div>
				</template>
			</component>
		</div>
		<transition name="fade">
			<div v-if="lazyLoading" class="lazy-loader">
				<v-spinner color="--blue-grey-300" background-color="--blue-grey-200" />
			</div>
		</transition>
	</div>
</template>

<script>
import isRelational from '@/helpers/is-relational';
import { isObject, isEqual, isNil } from 'lodash';

export default {
	name: 'VTable',
	props: {
		loading: {
			type: Boolean,
			default: false
		},
		lazyLoading: {
			type: Boolean,
			default: false
		},
		items: {
			type: Array,
			required: true
		},
		height: {
			type: Number,
			default: null
		},
		columns: {
			type: Array,
			required: true
		},
		link: {
			type: String,
			default: null
		},
		selection: {
			type: Array,
			default: null
		},
		sortVal: {
			type: Object,
			default: null
		},
		manualSortField: {
			type: String,
			default: null
		},
		primaryKeyField: {
			type: String,
			required: true
		},
		rowHeight: {
			type: Number,
			default: 48
		},
		columnWidths: {
			type: Object,
			default: null
		},
		useInterfaces: {
			type: Boolean,
			default: false
		},
		collection: {
			type: String,
			default: null
		}
	},
	data() {
		return {
			widths: {},
			lastDragXPosition: null,
			windowHeight: 0,
			scrolled: false,

			dragging: false,
			manualSorting: false,
			itemsManuallySorted: []
		};
	},
	computed: {
		allSelected() {
			const primaryKeyFields = this.items.map(item => item[this.primaryKeyField]).sort();
			const selection = [...this.selection];
			selection.sort();
			return this.selection.length > 0 && isEqual(primaryKeyFields, selection);
		},
		selectable() {
			return Array.isArray(this.selection);
		},
		sortable() {
			return isObject(this.sortVal);
		},
		resizeable() {
			return isObject(this.columnWidths);
		},
		totalWidth() {
			return (
				Object.keys(this.widths)
					.map(field => this.widths[field])
					.reduce((acc, val) => acc + val, 0) +
				30 +
				40 +
				(this.manualSorting ? 38 : 0)
			);
		},
		itemsArray() {
			return this.manualSorting ? this.itemsManuallySorted : this.items;
		}
	},
	watch: {
		columnWidths() {
			this.initWidths();
		},
		columns() {
			this.initWidths();
		},
		items(newVal) {
			this.itemsManuallySorted = newVal;
		}
	},
	created() {
		this.initWidths();

		if (!this.manualSortField) return;

		if (
			this.sortVal &&
			this.sortVal.field === this.manualSortField &&
			this.sortVal.asc === true
		) {
			this.manualSorting = true;
			this.itemsManuallySorted = this.items;
		}
	},
	methods: {
		isRelational: isRelational,
		isNil(val) {
			return isNil(val);
		},
		selectAll() {
			if (this.allSelected) {
				return this.$emit('select', []);
			}

			const primaryKeyFields = this.items.map(item => item[this.primaryKeyField]);
			return this.$emit('select', primaryKeyFields);
		},
		updateSort(field, direction) {
			this.manualSorting = false;

			if (direction) {
				const newSortVal = {
					field,
					asc: direction === 'asc'
				};
				return this.$emit('sort', newSortVal);
			}

			const newSortVal = {
				field,
				asc: field === this.sortVal.field ? !this.sortVal.asc : 'ASC'
			};

			this.$emit('sort', newSortVal);
		},
		toggleCheckbox(primaryKeyField) {
			const selection = [...this.selection];

			if (this.selection.includes(primaryKeyField)) {
				selection.splice(selection.indexOf(primaryKeyField), 1);
			} else {
				selection.push(primaryKeyField);
			}

			this.$emit('select', selection);
		},
		drag(field, event) {
			const { screenX } = event;

			if (screenX !== 0 && this.lastDragXPosition) {
				const delta = screenX - this.lastDragXPosition;

				const newPos = this.widths[field] + delta;
				this.widths[field] = newPos;
			}

			this.lastDragXPosition = screenX;
		},
		dragEnd() {
			this.lastDragXPosition = 0;
			this.$emit('widths', this.widths);
		},
		hideDragImage(event) {
			const img = document.createElement('img');
			img.src =
				'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
			event.dataTransfer.setDragImage(img, 0, 0);
			event.dataTransfer.effectAllowed = 'move';
			event.dataTransfer.setData('text/plain', null); //Just to enable/make the drag event to be triggered in the Firefox browser
		},
		initWidths() {
			const widths = {};

			this.columns.forEach(({ field }) => {
				const width = (this.columnWidths && this.columnWidths[field]) || 200;
				widths[field] = width > 0 ? width : 200;
			});

			this.widths = widths;
		},
		onScroll(event) {
			const { scrollHeight, clientHeight, scrollTop } = event.srcElement;
			const totalScroll = scrollHeight - clientHeight;
			const delta = totalScroll - scrollTop;
			if (delta <= 500) this.$emit('scroll-end');
			this.scrolled = scrollTop > 0;
		},
		startManualSorting() {
			if (this.manualSorting) {
				this.manualSorting = false;
				return;
			}

			this.updateSort(this.manualSortField, 'asc');
			this.manualSorting = true;
		},
		startSort() {
			this.dragging = true;
		},
		saveSort() {
			this.dragging = false;
			if (this.itemsManuallySorted.some(row => row[this.manualSortField] == null)) {
				return this.$emit(
					'input',
					this.itemsManuallySorted.map((row, i) => ({
						[this.primaryKeyField]: row[this.primaryKeyField],
						[this.manualSortField]: i + 1
					}))
				);
			}

			return this.$emit(
				'input',
				this.itemsManuallySorted.map((row, i) => ({
					[this.primaryKeyField]: row[this.primaryKeyField],
					[this.manualSortField]: this.items[i][this.manualSortField]
				}))
			);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-table {
	width: 100%;
	overflow: auto;
	-webkit-overflow-scrolling: touch;
	position: relative;
	max-height: calc(100vh - var(--header-height));
	padding-bottom: var(--page-padding-bottom);
	&.loading {
		overflow: hidden; //Avoids scrollbars when initially loading items.
		.body {
			transition: opacity var(--medium) var(--transition-in);
			opacity: 0.4;
		}
	}
}

.toolbar,
.row {
	display: flex;
	align-items: center;
	padding: 0 12px;
	border-bottom: 2px solid var(--table-row-border-color);
	box-sizing: content-box;
}

.toolbar {
	position: sticky;
	height: var(--header-height);
	left: 0;
	top: 0;
	z-index: +1;
	background-color: var(--page-background-color);
	border-color: var(--table-head-border-color);
	transition: box-shadow var(--fast) var(--transition-out);

	&.shadow {
		box-shadow: var(--box-shadow);
		transition: box-shadow var(--medium) var(--transition-in);
	}
}

.body {
	position: relative;
	transition: opacity var(--medium) var(--transition-out);
	opacity: 1;
	height: calc(100% - var(--header-height));
	overflow: auto;
	-webkit-overflow-scrolling: touch;
}

.drag-handle {
	width: 8px;
	height: 100%;
	cursor: col-resize;
	position: absolute;
	display: flex;
	justify-content: center;
	align-items: center;
	right: 10px;
	opacity: 0;
	transition: opacity var(--fast) var(--transition-out);
}

.drag-handle-line {
	background-color: var(--input-border-color);
	width: 2px;
	height: 60%;
	transition: background-color var(--fast) var(--transition);
}

.drag-handle:hover .drag-handle-line {
	background-color: var(--input-border-color-hover);
}

.toolbar:hover .drag-handle {
	opacity: 1;
	transition: opacity var(--medium) var(--transition-in);
}

.row {
	opacity: 1;
	background-color: var(--page-background-color);
	box-sizing: border-box;
}

.row.link:hover {
	background-color: var(--highlight);
	cursor: pointer;
}

.dragging .row.link:hover {
	background-color: var(--page-background-color);
}

.row.selected {
	background-color: var(--highlight);
}

.cell {
	flex-shrink: 0;
	flex-basis: 200px;
	padding-right: 20px;
	position: relative;
	overflow: hidden;
	max-height: 100%;
}

.cell:last-of-type {
	flex-grow: 1;
}

.empty {
	color: var(--empty-value);
}

.toolbar .cell:not(.select) {
	height: 100%;
	display: flex;
	align-items: center;
}

// Table column header
.sort {
	width: 100%;
	height: 100%;
	text-align: left;
	transition: color var(--fast) var(--transition);
	position: relative;

	&.active {
		//
	}

	&:hover {
		//
	}
}

.sort-icon {
	opacity: 0;
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	margin-left: 4px;
	&.asc {
		transform-origin: center 6px;
		transform: scaleY(-1);
	}
}

.active .sort-icon {
	opacity: 1;
}

.select,
.manual-sort {
	flex-basis: 30px;
	padding: 0;
	margin-left: -3px; /* Shift to accomodate material design icons checkbox */
	margin-right: 8px;
}

.toolbar .manual-sort {
	button {
		color: var(--input-border-color);
		transition: color var(--fast) var(--transition);

		&:hover {
			transition: none;
			color: var(--input-border-color-hover);
		}
	}

	&.active button {
		color: var(--input-border-color-focus);
	}
}

.body .manual-sort {
	cursor: not-allowed;
	color: var(--input-background-color-disabled);

	&.active {
		cursor: grab;
		cursor: -webkit-grab;
		color: var(--input-border-color);
	}
}

.sortable-drag {
	opacity: 0;
}

.dragging .sortable-chosen,
.sortable-chosen:active {
	background-color: var(--highlight) !important;
	color: var(--blue-grey-900);

	.manual-sort {
		color: var(--blue-grey-700);
	}
}

.loader {
	div {
		animation: bounce 1s var(--transition) infinite alternate;
	}

	$elements: 50;
	@for $i from 0 to $elements {
		div:nth-child(#{$i + 1}) {
			animation-delay: $i * 100ms;
		}
	}
}

@keyframes bounce {
	from {
		border-color: var(--table-row-border-color);
	}

	to {
		border-color: var(--table-head-border-color);
	}
}

.lazy-loader {
	pointer-events: none;
	display: flex;
	justify-content: center;
	align-items: center;
	opacity: 1;
	transform: translateY(50px);
}

.fade-enter-active {
	transition: var(--slow) var(--transition-in);
}

.fade-leave-active {
	transition: var(--slow) var(--transition-out);
}

.fade-enter,
.fade-leave-to {
	opacity: 0;
}

.ellipsis {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
</style>
