const axios = require('axios');
const api = require('./api');
const fs = require('fs');
const url = require('url');
const path = require('path');
const { Liquid } = require('liquidjs');
const htmlmin = require('html-minifier');
const yaml = require('js-yaml');

module.exports = eleventyConfig => {

	//////////////////////////////////////////////////////////////////////////////
	// Register custom filters

	// Dump the value. Useful for debugging JSON structures
	eleventyConfig.addFilter('dump', function(value) {
		return JSON.stringify(value, null, 4);
	});

	// Converts JS date compatible string to `Month date, year` (December 19, 2019)
	eleventyConfig.addFilter('formatdate', function(value) {
		return new Date(value).toLocaleDateString('en-US', {
			year: 'numeric', month: 'long', day: 'numeric'
		});
	});

	// URI encode value
	eleventyConfig.addFilter('encodeuri', function(value) {
		return encodeURIComponent(value);
	});

	// Convert CSV to hashtags
	eleventyConfig.addFilter('tags', function(value) {
		console.log(value.toString());
		var tags = value.toString().split(",");
		tags.forEach(function (item, index) {
		  console.log(item, index);
		  tags[index] = '<span>#' + item + '</span>';
		});
		return tags.join("");
	});

	//////////////////////////////////////////////////////////////////////////////
	// Register custom shortcodes

	// Cache external images to the local filesystem
	// @usage
	//
	// With a full URL
	// {% cachefile 'https://example.com/hello.jpg' %}
	//
	// With a Directus file private_hash (full size)
	// {% cachefile article.image.private_hash %}
	//
	// With a Directus file private_hash + thumbnail key
	// {% cachefile article.image.private_hash, 'directus-medium-crop' %}

	eleventyConfig.addLiquidShortcode('cachefile', async function(value, thumbnailSize) {
		// Treat passed in value is private_hash if it's not a full URL
		if (value.startsWith('http') === false) {
			value = '/assets/' + value;

			if (thumbnailSize) {
				value += `?key=${thumbnailSize}`;
			}
		}

		const filename = path.basename(url.parse(value).pathname);

		const file = fs.createWriteStream(`./dist/images/${filename}`);

		const response = await api({
			method: 'GET',
			url: value,
			responseType: 'stream'
		});

		response.data.pipe(file);

		return new Promise((resolve, reject) => {
			file.on('finish', function() {
				resolve(`/images/${filename}`);
			});

			file.on('error', reject);
		});
	});

	// Return the current year
	eleventyConfig.addShortcode('year', function() {
		return new Date().getFullYear();
	});

	//////////////////////////////////////////////////////////////////////////////
	// Don't use Eleventy for any of these files (move them to dist as-is)

	eleventyConfig.addPassthroughCopy('src/favicons');
	eleventyConfig.addPassthroughCopy('src/fonts');
	eleventyConfig.addPassthroughCopy('src/images');

	//////////////////////////////////////////////////////////////////////////////
	// Use Liquid for templates

	eleventyConfig.setLibrary('liquid', new Liquid({
		extname: '.liquid',
		dynamicPartials: true,
		strict_filters: true,
		root: path.resolve(__dirname, 'src', '_includes')
	}));

	//////////////////////////////////////////////////////////////////////////////
	// Minify output HTML

	eleventyConfig.addTransform('htmlmin', function(content, outputPath) {
		if (outputPath.endsWith('.html')) {
			return htmlmin.minify(content, {
				useShortDoctype: true,
				removeComments: true,
				collapseWhitespace: true
			});
		}

		return content;
	});

	//////////////////////////////////////////////////////////////////////////////
	// Read .yml files in _data

	eleventyConfig.addDataExtension("yml", function(content) {
		return yaml.safeLoad(content);
	});

	//////////////////////////////////////////////////////////////////////////////
	// Set final Eleventy config

	return {
		dir: { input: 'src', output: 'dist', data: '_data' },
		passthroughFileCopy: true,
		templateFormats: ['liquid', 'md', 'css', 'html', 'yml'],
		htmlTemplateEngine: 'liquid'
	};

	//////////////////////////////////////////////////////////////////////////////
};
