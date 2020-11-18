const axios = require('axios');

const api = axios.create({
	baseURL: 'https://demo.directus.io/thumper/',
	headers: { Authorization: 'Bearer admin' }
});

module.exports = api;
