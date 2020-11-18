export function editing(state) {
	// eslint-disable-line
	const collectionExists = state.collection && state.collection.length > 0;
	const primaryKeyExists = state.primaryKey && state.primaryKey.length > 0;
	const valuesExist = state.values && Object.keys(state.values).length > 0;
	const saving = state.saving;

	return (collectionExists && primaryKeyExists && valuesExist && !saving) || false;
}
