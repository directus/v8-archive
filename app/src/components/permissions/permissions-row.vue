<template>
	<div class="v-permissions-row" :class="{ 'system-row': system }">
		<div v-if="!statuses" class="row">
			<div class="cell collection-title">
				<span v-tooltip="permissionName" :class="{ system }" class="name">
					{{
						$helpers.formatTitle(system ? permissionName.substring(9) : permissionName)
					}}
				</span>
				<span class="set-all">
					<button class="on" type="button" @click.prevent="setAll(true)">
						<v-icon
							v-tooltip="$t('turn_all_on')"
							class="icon"
							name="done"
							small
							color="--success"
						/>
					</button>
					<button class="off" type="button" @click.prevent="setAll(false)">
						<v-icon
							v-tooltip="$t('turn_all_off')"
							class="icon"
							name="block"
							small
							color="--danger"
						/>
					</button>
				</span>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="permission.create"
					:options="['none', 'full']"
					@input="emitValue('create', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="permission.read"
					:options="permissionOptions"
					@input="emitValue('read', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="permission.update"
					:options="permissionOptions"
					@input="emitValue('update', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="permission.delete"
					:options="permissionOptions"
					@input="emitValue('delete', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="permission.comment"
					:options="['none', 'read', 'create', 'update', 'full']"
					@input="emitValue('comment', $event)"
				/>
			</div>
			<!--
        <div class="cell">
          <v-permissions-toggle
            :value="permission.explain"
            :options="['none', 'create', 'update', 'always']"
            @input="emitValue('explain', $event);"
          />
        </div>
      -->
			<div class="cell">
				<button
					:class="{ limited: fieldState }"
					@click="fieldsSelect = { collection: permissionName }"
				>
					{{ fieldState ? $t('limited') : $t('all') }}
				</button>
			</div>
			<div class="cell"><span class="mixed">--</span></div>
		</div>
		<div v-else class="row">
			<div class="cell collection-title">
				<span v-tooltip="permissionName" :class="{ system }" class="name">
					{{
						$helpers.formatTitle(system ? permissionName.substring(9) : permissionName)
					}}
				</span>
				<span class="set-all">
					<button class="on" type="button" @click.prevent="setAll(true)">
						<v-icon
							v-tooltip="$t('turn_all_on')"
							class="icon"
							name="done"
							small
							color="--success"
						/>
					</button>
					<button class="off" type="button" @click.prevent="setAll(false)">
						<v-icon
							v-tooltip="$t('turn_all_off')"
							class="icon"
							name="block"
							small
							color="--danger"
						/>
					</button>
				</span>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="getCombinedVal('create')"
					:options="['none', 'full']"
					@input="setAllStatuses('create', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="getCombinedVal('read')"
					:options="permissionOptions"
					@input="setAllStatuses('read', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="getCombinedVal('update')"
					:options="permissionOptions"
					@input="setAllStatuses('update', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="getCombinedVal('delete')"
					:options="permissionOptions"
					@input="setAllStatuses('delete', $event)"
				/>
			</div>
			<div class="cell">
				<v-permissions-toggle
					:value="getCombinedVal('comment')"
					:options="['none', 'create', 'update', 'full']"
					@input="setAllStatuses('comment', $event)"
				/>
			</div>
			<!--
        <div class="cell">
          <v-permissions-toggle
            :value="getCombinedVal('explain')"
            :options="['none', 'create', 'update', 'always']"
            @input="setAllStatuses('explain', $event);"
          />
        </div>
      -->
			<div class="cell">
				<button :class="{ limited: fieldState }" @click="active = !active">
					{{ fieldState ? $t('mixed') : $t('all') }}
				</button>
			</div>
			<div class="cell">
				<button class="mixed" @click="active = !active">Expand</button>
			</div>
		</div>
		<template v-if="active">
			<div class="sub row">
				<div v-tooltip="'System Option'" class="cell">
					{{ $t('permission_states.on_create') }}
				</div>
				<div class="cell block"><v-icon name="block" /></div>
				<div class="cell block"><v-icon name="block" /></div>
				<div class="cell block"><v-icon name="block" /></div>
				<div class="cell block"><v-icon name="block" /></div>
				<div class="cell block"><v-icon name="block" /></div>
				<div class="cell">
					<button
						:class="{ limited: getFieldSettingsPerStatus('$create') }"
						@click="fieldsSelect = { collection: permissionName, status: '$create' }"
					>
						{{ getFieldSettingsPerStatus('$create') ? $t('limited') : $t('all') }}
					</button>
				</div>
				<div v-if="statuses" class="cell">
					<button
						:class="{
							limited: (permission.$create.status_blacklist || []).length !== 0
						}"
						@click="statusSelect = { collection: permissionName, status: '$create' }"
					>
						{{
							(permission.$create.status_blacklist || []).length === 0
								? $t('all')
								: $t('limited')
						}}
					</button>
				</div>
				<div v-else class="cell"><span class="mixed">--</span></div>
			</div>
		</template>
		<template v-if="statuses && active">
			<div
				v-for="(statusInfo, status) in statuses"
				:key="`${permissionName}-${status}`"
				class="sub row"
			>
				<div class="cell">
					<span v-tooltip="status">{{ statusInfo.name }}</span>
				</div>
				<div class="cell">
					<v-permissions-toggle
						:value="permission[status].create"
						:options="['none', 'full']"
						@input="emitValue('create', $event, status)"
					/>
				</div>
				<div class="cell">
					<v-permissions-toggle
						:value="permission[status].read"
						:options="permissionOptions"
						@input="emitValue('read', $event, status)"
					/>
				</div>
				<div class="cell">
					<v-permissions-toggle
						:value="permission[status].update"
						:options="permissionOptions"
						@input="emitValue('update', $event, status)"
					/>
				</div>
				<div class="cell">
					<v-permissions-toggle
						:value="permission[status].delete"
						:options="permissionOptions"
						@input="emitValue('delete', $event, status)"
					/>
				</div>
				<div class="cell">
					<v-permissions-toggle
						:value="permission[status].comment"
						:options="['none', 'create', 'update', 'full']"
						@input="emitValue('comment', $event, status)"
					/>
				</div>
				<!--
          <div class="cell">
            <v-permissions-toggle
              :value="permission[status].explain"
              :options="['none', 'create', 'update', 'always']"
              @input="emitValue('explain', $event, status);"
            />
          </div>
        -->
				<div class="cell">
					<button
						:class="{ limited: getFieldSettingsPerStatus(status) }"
						@click="fieldsSelect = { collection: permissionName, status }"
					>
						{{ getFieldSettingsPerStatus(status) ? $t('limited') : $t('all') }}
					</button>
				</div>
				<div class="cell">
					<button
						:class="{
							limited: (permission[status].status_blacklist || []).length !== 0
						}"
						@click="statusSelect = { collection: permissionName, status }"
					>
						{{
							(permission[status].status_blacklist || []).length === 0
								? $t('all')
								: $t('limited')
						}}
					</button>
				</div>
			</div>
		</template>
		<button class="collapse" @click="active = !active">
			<v-icon :name="active ? 'unfold_less' : 'unfold_more'" />
		</button>
		<portal v-if="fieldsSelect" to="modal">
			<v-modal
				:title="$t('select_fields')"
				:buttons="{ confirm: { text: $t('confirm') } }"
				action-required
				@confirm="fieldsSelect = null"
			>
				<form class="modal-content" @submit.prevent>
					<fieldset>
						<legend class="type-label">{{ $t('readable_fields') }}</legend>
						<v-checkbox
							v-for="(field, name) in fieldsWithoutPK"
							:id="`${permissionName}-read-${name}`"
							:key="`${permissionName}-read-${name}`"
							:inputValue="!blacklist.read.includes(name)"
							:label="$helpers.formatTitle(name)"
							:value="name"
							@change="toggleField(name)"
						/>
					</fieldset>
					<fieldset>
						<legend class="type-label">{{ $t('writable_fields') }}</legend>
						<v-checkbox
							v-for="(field, name) in fields"
							:id="`${permissionName}-write-${name}`"
							:key="`${permissionName}-write-${name}`"
							:inputValue="!blacklist.write.includes(name)"
							:label="$helpers.formatTitle(name)"
							:value="name"
							@change="toggleField(name, true)"
						/>
					</fieldset>
				</form>
			</v-modal>
		</portal>
		<portal v-if="statusSelect && statuses" to="modal">
			<v-modal
				:title="$t('select_statuses')"
				:buttons="{ confirm: { text: $t('confirm') } }"
				action-required
				@confirm="statusSelect = null"
			>
				<form class="modal-content" @submit.prevent>
					<fieldset>
						<legend class="type-label">{{ $t('allowed_status_options') }}</legend>
						<v-checkbox
							v-for="(status, name) in statuses"
							:id="`status-${name}`"
							:key="`status-${name}`"
							:inputValue="
								!(permission[statusSelect.status].status_blacklist || []).includes(
									name
								)
							"
							:label="status.name"
							:value="name"
							@change="toggleStatus(name)"
						/>
					</fieldset>
				</form>
			</v-modal>
		</portal>
	</div>
</template>

<script>
import VPermissionsToggle from './permissions-toggle.vue';
import { find, forEach } from 'lodash';

export default {
	name: 'VPermissionsRow',
	components: {
		VPermissionsToggle
	},
	props: {
		permission: {
			type: Object,
			required: true
		},
		statuses: {
			type: Object,
			default: null
		},
		permissionName: {
			type: String,
			required: true
		},
		fields: {
			type: Object,
			default: () => ({})
		},
		system: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			active: false,
			fieldsSelect: false,
			statusSelect: false
		};
	},
	computed: {
		primaryKeyFieldName() {
			return find(this.fields, { primary_key: true }).field;
		},
		fieldsWithoutPK() {
			const fieldsCopy = Object.assign({}, this.fields);

			delete fieldsCopy[this.primaryKeyFieldName];

			return fieldsCopy;
		},
		collapsable() {
			return this.statuses != null;
		},
		blacklist() {
			if (!this.fieldsSelect || !this.permission) return;

			const { status } = this.fieldsSelect;

			const permissionInfo = status ? this.permission[status] : this.permission;

			return {
				read: permissionInfo.read_field_blacklist || [],
				write: permissionInfo.write_field_blacklist || []
			};
		},
		/**
		 * Returns if fields have been configured for this row
		 * @return {Boolean}
		 */
		fieldState() {
			if (!this.permission) return this.$t('all');

			if (this.statuses) {
				let all = true;

				forEach(this.permission, permission => {
					if (
						permission.read_field_blacklist.length > 0 ||
						permission.write_field_blacklist.length > 0
					) {
						all = false;
					}
				});

				return !all;
			}

			const readBlacklist = this.permission.read_field_blacklist || [];
			const writeBlacklist = this.permission.write_field_blacklist || [];

			if (readBlacklist.length === 0 && writeBlacklist.length === 0) {
				return false;
			}

			return true;
		},
		userCreatedField() {
			return find(this.fields, field => field.type && field.type.toLowerCase() === 'owner');
		},
		permissionOptions() {
			// To provide all options for core table as well as those collections which contains usercreated field
			return this.userCreatedField || this.permissionName.startsWith('directus_')
				? ['none', 'mine', 'role', 'full']
				: ['none', 'full'];
		}
	},
	methods: {
		setAll(enabled = true) {
			const newPermission = enabled
				? {
						create: 'full',
						read: 'full',
						update: 'full',
						delete: 'full',
						comment: 'full',
						explain: 'none',
						read_field_blacklist: [],
						write_field_blacklist: [],
						status_blacklist: []
				  }
				: {
						create: 'none',
						read: 'none',
						update: 'none',
						delete: 'none',
						comment: 'none',
						explain: 'none',
						read_field_blacklist: [],
						write_field_blacklist: [],
						status_blacklist: []
				  };

			if (this.statuses) {
				const changes = [];

				Object.keys(this.statuses).forEach(status => {
					Object.keys(newPermission).forEach(permission => {
						changes.push({
							collection: this.permissionName,
							permission,
							value: newPermission[permission],
							status
						});
					});
				});

				return this.$emit('input', changes);
			}

			return this.$emit(
				'input',
				Object.keys(newPermission).map(permission => ({
					collection: this.permissionName,
					permission,
					value: newPermission[permission]
				}))
			);
		},
		emitValue(permission, value, status = null) {
			this.$emit('input', {
				collection: this.permissionName,
				permission,
				value,
				status
			});
		},
		getCombinedVal(field) {
			if (!this.statuses) return null;

			let value = this.permission[Object.keys(this.statuses)[0]][field];

			forEach(this.permission, (status, name) => {
				if (name !== '$create' && status[field] !== value) value = 'indeterminate';
			});

			return value;
		},
		/**
		 * If field settings have been configured or not per status
		 * @param  {String} status Status name
		 * @return {Boolean}
		 */
		getFieldSettingsPerStatus(status) {
			const readBlacklist = this.permission[status].read_field_blacklist;
			const writeBlacklist = this.permission[status].write_field_blacklist;

			if (readBlacklist.length === 0 && writeBlacklist.length === 0) {
				return false;
			}

			return true;
		},
		setAllStatuses(field, value) {
			Object.keys(this.statuses).forEach(status => {
				this.emitValue(field, value, status);
			});
		},
		toggleField(field, write = false) {
			const { status } = this.fieldsSelect;

			const selectedFields = write ? this.blacklist.write : this.blacklist.read;

			const permissionField = write ? 'write_field_blacklist' : 'read_field_blacklist';

			if (selectedFields.includes(field)) {
				return this.emitValue(
					permissionField,
					selectedFields.filter(f => f !== field),
					status
				);
			}

			return this.emitValue(permissionField, [...selectedFields, field], status);
		},
		toggleStatus(status) {
			if (!this.statuses) return;

			const parentStatus = this.statusSelect.status;
			const selectedStatuses = this.permission[parentStatus].status_blacklist;

			console.log([...selectedStatuses, status], parentStatus);

			if (selectedStatuses.includes(status)) {
				return this.emitValue(
					'status_blacklist',
					selectedStatuses.filter(s => s !== status),
					parentStatus
				);
			}

			return this.emitValue('status_blacklist', [...selectedStatuses, status], parentStatus);
		}
	}
};
</script>

<style lang="scss" scoped>
.v-permissions-row {
	position: relative;
}

fieldset {
	padding: 0;
}

.collapse {
	position: absolute;
	top: 10px;
	right: 3px;

	i {
		color: var(--input-icon-color);
		transition: color var(--fast) var(--transition);
	}

	&:hover i {
		color: var(--input-text-color);
	}
}

.modal-content {
	padding: var(--form-vertical-gap) 20px;

	fieldset:not(:last-of-type) {
		margin-bottom: var(--form-vertical-gap);
	}

	legend.type-label {
		margin: 0;
		padding: 0;
		margin-bottom: var(--input-label-margin);
	}

	p {
		margin-bottom: 10px;
	}
}

.mixed {
	color: var(--empty-value);
}

.limited {
	color: var(--warning);
}

.block {
	color: var(--input-border-color);
}

.collection-title {
	position: relative;
	margin-right: 4px;
	.name {
		display: block;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		width: 100%;
	}
	.set-all {
		position: absolute;
		top: -4px;
		right: 4px;
		opacity: 0;
		padding: 4px 4px 4px 16px;
		background-color: var(--page-background-color);
		background: linear-gradient(90deg, transparent 0%, var(--page-background-color) 25%);
		transition: opacity var(--fast) var(--transition);

		button {
			transition: color var(--fast) var(--transition);
			margin-left: 4px;
		}
	}
}

.v-permissions-row:hover .set-all {
	opacity: 1;
	transition: none;

	button:hover {
		transition: none;
	}

	button:first-of-type:hover {
		color: var(--success);
	}

	button:last-of-type:hover {
		color: var(--danger);
	}
}

.system-row {
	color: var(--input-placeholder-color);
}
</style>
