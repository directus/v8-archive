const api = require('../../api');

module.exports = async function fetch() {
	const response = await api.get('/items/news', {
		params: {
			fields: ['body', 'title', 'summary', 'tags', 'author.first_name', 'author.last_name', 'published_on']
		}
	});
	return response.data.data;
}
