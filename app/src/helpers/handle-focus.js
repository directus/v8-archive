function handleFirstTab(e) {
	if (e.key && e.key.toLowerCase() === 'tab') {
		document.body.classList.add('user-is-tabbing');

		window.removeEventListener('keydown', handleFirstTab);
		window.addEventListener('mousedown', handleMouseDownOnce); // eslint-disable-line no-use-before-define
	}
}

function handleMouseDownOnce() {
	document.body.classList.remove('user-is-tabbing');

	window.removeEventListener('mousedown', handleMouseDownOnce);
	window.addEventListener('keydown', handleFirstTab);
}

window.addEventListener('keydown', handleFirstTab);
