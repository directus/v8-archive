const api = require('../../api');

module.exports = async function fetch() {
	const response = await api.get('/items/locations', {
		params: {
			fields: ['*.*']
		}
	});

	return response.data.data;
}
