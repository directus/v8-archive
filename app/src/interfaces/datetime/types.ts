export type DateTimeOptions = {
	min: string;
	max: string;
	localized: boolean;
	showRelative: boolean;
	iconLeft: string;
	iconRight: string;
	defaultToCurrentDateTime: boolean;
	format: 'mm/dd/yyyy hh:mm:ss' | 'dd/mm/yyyy hh:mm:ss' | 'yyyy-mm-dd hh:mm:ss';
};
