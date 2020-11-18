/**
 * Extracts the fields from a template string
 * @param  {String} string Template string
 * @return {Array}         Field names
 * @example
 *
 * getFieldsFromTemplate("{{ title }} {{ runtime }}");
 * // -> ["title", "runtime"]
 */
export default function getFieldsFromTemplate(string) {
	const regex = /{{(.*?)}}/g;
	let fields = string.match(regex);

	if (!Array.isArray(fields)) {
		return [];
	}

	fields = fields.map(field => {
		return field
			.replace(/{{/g, '')
			.replace(/}}/g, '')
			.trim();
	});
	return fields;
}
