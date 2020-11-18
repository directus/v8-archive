/**
 * Load an external script. Appends a <script> to the body to avoid CORS issues
 * @param  {String}   src Location of the file on the web
 * @return {Promise}      Resolves the extension module, rejects loading error
 */
export default function loadExtension(src) {
	return new Promise((resolve, reject) => {
		const script = document.createElement('script');
		const link = document.createElement('link');

		function cleanup() {
			script.remove();
			window.__DirectusExtension__ = null; // eslint-disable-line no-underscore-dangle
		}

		function onload() {
			const extensionModule = window.__DirectusExtension__; // eslint-disable-line no-underscore-dangle, max-len
			resolve(extensionModule);
			cleanup();
		}

		function onerror(err) {
			reject(err);
			cleanup();
		}

		link.rel = 'stylesheet';

		// NOTE:
		// The src is always a .js file. We can retrieve the extension's CSS by
		//   by fetching the same path with the css extension
		link.href = src.slice(0, -2) + 'css';
		link.onerror = function() {
			this.remove();
		};

		script.onload = onload;
		script.onerror = onerror;
		script.src = src;
		document.body.appendChild(script);
		document.body.appendChild(link);
	});
}
