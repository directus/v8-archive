<template>
	<div
		:class="options.theme ? `button-group-${options.theme}` : 'button-group-outline'"
		class="interface-button-group">
		<div
			v-for="(item, index) in choices"
      :key="`button-group-subgroup-${index}`"
			class="button-group-subgroup">
			<label
				v-for="(subitem, index) in item"
        :key="`button-group-item-${index}`"
				class="button-group-item">
				<input
					type="radio"
					:name="name"
					:disabled="readonly"
					@change="$emit('input', subitem.value)"
					:value="subitem.value">
				<span class="button-group-button">
					<i v-if="subitem.icon" class="material-icons">{{subitem.icon}}</i>
					<span v-if="subitem.label">{{subitem.label}}</span>
				</span>
			</label>
		</div>
	</div>
</template>

<script>
import mixin from "../../../mixins/interface";

export default {
  name: "interface-button-group",
  mixins: [mixin],
  computed: {
    choices() {
      /**
       * We'll create an array of choices here.
       * If the button-group has subgroups of choices & indivisual choice both,
       * We'll need to create a new subgroup with all individual items.
       */
      const choices = [];
      const individualChoices = [];

      this.options.choices.forEach(item => {
        if (Array.isArray(item)) {
          choices.push(item);
        } else {
          individualChoices.push(item);
        }
      });

      choices.push(individualChoices);
      return choices;
    }
  }
};
</script>

<style lang="scss" scoped>
/*
Theme: Outline | Default
*/
.button-group-subgroup {
  display: inline-flex;
  flex-wrap: wrap;
  margin-right: 10px;
}

.button-group-button {
  border: var(--input-border-width) solid var(--action);
  cursor: pointer;
  transition: var(--fast) var(--transition);
  transition-property: border-color, background-color, color;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0px 20px;
  height: 40px;
  line-height: 40px;
  white-space: nowrap;
  color: var(--action-dark);

  &:hover {
    background-color: var(--light-blue-50);
  }

  i {
    font-size: 18px;

    + span {
      margin-left: 4px;
    }
  }
}

.button-group-item {
  input[type="radio"] {
    height: 0;
    position: absolute;
    opacity: 0;
    /**
			Focused State
		*/
    &:focus {
      + .button-group-button {
        background-color: var(--light-blue-50);
      }
    }
    /**
			Checked State
		*/
    &:checked {
      + .button-group-button {
        background-color: var(--accent);
        color: var(--white);
      }
    }
    /**
			Disabled State
		*/
    &:disabled {
      + .button-group-button {
        border-color: var(--lighter-gray);
        background-color: var(--lightest-gray);
        color: var(--gray);
        cursor: not-allowed;
      }
      &:checked {
        + .button-group-button {
          background-color: var(--lighter-gray);
          color: var(--gray);
        }
      }
    }
  }

  + .button-group-item {
    .button-group-button {
      margin-left: calc(-1 * var(--input-border-width));
    }
  }

  &:first-child {
    .button-group-button {
      border-radius: var(--border-radius) 0 0 var(--border-radius);
    }
  }

  &:last-child {
    .button-group-button {
      border-radius: 0 var(--border-radius) var(--border-radius) 0;
    }
  }
}

@media only screen and (max-width: 800px) {
  .interface-button-group {
    display: inline-flex;
    flex-direction: column;
  }

  .button-group-subgroup {
    flex-direction: column;
    display: inline-flex;
    margin: 0;

    + .button-group-subgroup {
      margin: 10px 0 0 0;
    }
  }

  .button-group-item {

    + .button-group-item {
      .button-group-button {
        margin-left: 0;
        margin-top: calc(-1 * var(--input-border-width));
      }
    }

    &:first-child {
      .button-group-button {
        border-radius: var(--border-radius) var(--border-radius) 0 0;
      }
    }

    &:last-child {
      .button-group-button {
        border-radius: 0 0 var(--border-radius) var(--border-radius);
      }
    }
  }
}

/*
Theme: Solid
*/
.button-group-solid {
  .button-group-button {
    border: none;
    background-color: var(--dark-gray);
    color: var(--white);
    &:hover {
      background-color: var(--darker-gray);
      color: var(--white);
    }
  }

  .button-group-item {
    input[type="radio"] {
      /**
				Focused State
			*/
      &:focus {
        + .button-group-button {
          background-color: var(--darker-gray);
        }
      }
      &:checked {
        + .button-group-button {
          background-color: var(--accent);
        }
      }
      /**
				Disabled State
			*/
      &:disabled {
        + .button-group-button {
          background-color: var(--lighter-gray);
          color: var(--light-gray);
        }
        &:checked {
          + .button-group-button {
            background-color: var(--blue-grey-200);
            color: var(--gray);
          }
        }
      }
    }

    + .button-group-item {
      .button-group-button {
        margin-left: 0;
      }
    }
  }

  @media only screen and (max-width: 800px) {
    .button-group-item {
      + .button-group-item {
        .button-group-button {
          margin-top: 0;
        }
      }
    }
  }
}
</style>
