<template>
	<div class="interface-tags">
		<v-input
			class="input"
			type="text"
			:placeholder="options.placeholder || $t('interfaces.tags.placeholder_text')"
			:icon-left="options.iconLeft"
			:icon-right="options.iconRight"
			@keydown="onInput"
		></v-input>
		<div v-if="valueArray.length > 0" class="buttons">
			<v-tag
				v-for="value in valueArray"
				:key="value"
				clickable
				@click.prevent="removeTag(value)"
			>
				{{ value }}
			</v-tag>
		</div>
	</div>
</template>

<script>
import mixin from '@directus/extension-toolkit/mixins/interface';

export default {
	mixins: [mixin],

	data() {
		return {
			valueArray: []
		};
	},

	// Make sure to re-populate the local state if the value changes from outside
	// of the interface
	watch: {
		value() {
			this.getLocalValueArray();
		}
	},

	created() {
		this.getLocalValueArray();
	},

	methods: {
		// If the user is typing and hits Enter or `,` add the tag
		onInput(event) {
			if ((event.target.value && event.key === 'Enter') || event.key === ',') {
				event.preventDefault();
				this.addTag(event.target.value);
				event.target.value = '';
			}
		},

		addTag(tag) {
			if (!tag) return;

			// Remove any leading / trailing whitespace from the value
			tag = tag.trim();

			// Convert the tag to lowercase
			if (this.options.lowercase) {
				tag = tag.toLowerCase();
			}

			// Clean up the tag
			if (this.options.sanitize) {
				tag = tag
					// Replace all non alphanumeric characters with a hyphen
					.replace(/([^a-z0-9]+)/gi, '-')
					// Remove leading / trailing hyphens and remove doubles
					.replace(/^-|-$/g, '');
			}

			// If there is a validation regex option, only add if matches
			if (this.options.validation) {
				const regex = RegExp(this.options.validation);
				let message = this.options.validationMessage
					? this.options.validationMessage
					: this.$t('interfaces.tags.validation_message_default');
				if (!regex.test(tag)) {
					this.$notify({
						title: message,
						color: 'amber',
						iconMain: 'local_offer'
					});
					return;
				}
			}

			let valueArrayCopy = this.valueArray.splice(0);

			valueArrayCopy.push(tag);

			if (this.options.alphabetize) {
				valueArrayCopy.sort();
			}

			// Remove any duplicates
			valueArrayCopy = [...new Set(valueArrayCopy)];

			// Set the local value to reflect it in the interface
			this.valueArray = valueArrayCopy;

			this.emitValue();
		},

		removeTag(tag) {
			this.valueArray = this.valueArray.filter(savedTag => savedTag !== tag);
			this.emitValue();
		},

		emitValue() {
			// Convert the value array to a CSV
			let value = this.valueArray.join(',');

			if (value && this.options.wrap) {
				value = ',' + value + ',';
			}

			if (this.type === 'array') {
				this.$emit('input', value.split(','));
			} else {
				this.$emit('input', value);
			}
		},

		getLocalValueArray() {
			let array;

			// If the value is null or empty...
			if (Boolean(this.value) === false) {
				this.valueArray = [];
				return;
			}

			if (Array.isArray(this.value)) {
				array = this.value;
			} else {
				array = this.value.split(',');
			}

			// The wrap option will introduce empty values at the beginning and end of
			// the value. We'll filter out all the empty (falsey) values from the array
			array = array.filter(value => value);

			this.valueArray = array;
		}
	}
};
</script>

<style lang="scss" scoped>
.interface-tags {
	max-width: var(--width-medium);
}

.buttons {
	display: flex;
	flex-wrap: wrap;
	padding: 5px 0;
}
</style>
