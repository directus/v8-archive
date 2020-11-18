/**
 * Flatten a filters array to a single object that can be used by the SDK
 * @param  {Array} filters  The filters to transform
 * @return {Object}         The filters query param
 *
 * @example
 *
 * const filters = [
 *   {
 *     field: 'title',
 *     operator: 'contains',
 *     value: 'Directus'
 *   }
 * ];
 *
 * formatFilters(filters);
 * // => {
 *   'filter[title][contains]': 'Directus'
 * }
 */
export default function formatFilters(filters) {
	const parsedFilters = {};

	filters.forEach(filter => {
		parsedFilters[`filter[${filter.field}][${filter.operator}]`] = filter.value;
	});

	return parsedFilters;
}
