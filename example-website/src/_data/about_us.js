const api = require('../../api');

module.exports = async function fetch() {
	const response = await api.get('/items/about_us/1');
	return response.data.data;
}
