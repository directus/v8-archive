/**
 * Convert SQL datetime string to JavaScript Date object
 * @param  {String} sqlDate SQL date-time string YYYY-MM-DD HH:MM:SS
 * @return {Date}           JavaScript Date object
 */
function sqlToDate(sqlDate) {
	const t = sqlDate.split(/[- :]/);
	return new Date(Date.UTC(t[0], t[1] - 1, t[2], t[3], t[4], t[5]));
}

/**
 * Convert JS Date object to SQL datetime string
 * @param  {Date} date JavaScript Date date
 * @return {String}    SQL datetime string YYYY-MM-DD HH:MM:SS
 */
function dateToSql(date) {
	return date
		.toISOString()
		.slice(0, 19)
		.replace('T', ' ');
}

export default {
	sqlToDate,
	dateToSql
};
