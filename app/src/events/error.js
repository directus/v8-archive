import notify from '../notifications';
import { isEmpty, isUndefined } from 'lodash';

const handleError = error => {
	if (!isEmpty(error.message)) {
		console.error(error.message); //eslint-disable-line no-console
	}
	if (!isUndefined(error.error)) {
		console.error(error.error); //eslint-disable-line no-console
	}
	if (!isEmpty(error.notify)) {
		notify({
			title: error.notify,
			color: 'red',
			iconMain: 'error'
		});
	}
};

export default handleError;
