/**
 * Check if a given hex color is too light to have white things on it
 */
export default function tooLight(hex) {
	hex = hex.substring(1); // strip #
	const rgb = parseInt(hex, 16); // convert rrggbb to decimal
	const r = (rgb >> 16) & 0xff; // extract red
	const g = (rgb >> 8) & 0xff; // extract green
	const b = (rgb >> 0) & 0xff; // extract blue

	const luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // 0..255, where 0 is the darkest and 255 is the lightest — per ITU-R BT.709
	return luma > 200 ? true : false;
}
