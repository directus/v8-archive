type sizeProps = {
	sup: boolean;
	xSmall: boolean;
	small: boolean;
	large: boolean;
	xLarge: boolean;
};

export default function getSizeClass(props: sizeProps): string | null {
	if (props.sup) return 'sup';
	if (props.xSmall) return 'x-small';
	if (props.small) return 'small';
	if (props.large) return 'large';
	if (props.xLarge) return 'x-large';
	return null;
}
