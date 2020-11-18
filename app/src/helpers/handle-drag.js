let timeout;
let dragging = false;

document.body.addEventListener(
	'dragenter',
	() => {
		dragging = true;
		document.body.classList.add('dragging');
	},
	false
);

document.body.addEventListener(
	'dragover',
	() => {
		dragging = true;
	},
	false
);

document.body.addEventListener('dragleave', remove, false);
document.body.addEventListener('dragexit', remove, false);
document.body.addEventListener('dragend', remove, false);
document.body.addEventListener('drop', remove, false);

function remove() {
	dragging = false;
	clearTimeout(timeout);
	timeout = setTimeout(() => {
		if (dragging === false) document.body.classList.remove('dragging');
	}, 50);
}
