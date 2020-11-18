/**
 * Checks if a field field is relational
 * @param  {Object}  fieldInfo
 * @return {Boolean}
 */
export default function isRelational(fieldInfo) {
	if (!fieldInfo) return false;

	const type = fieldInfo.type?.toLowerCase();

	switch (type) {
		case 'o2m':
		case 'm2o':
		case 'user':
		case 'owner':
		case 'user_updated':
		case 'alias':
		case 'translation':
		case 'file':
			return true;
		default:
			return false;
	}
}
