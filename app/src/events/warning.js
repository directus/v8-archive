import notify from '../notifications';
import { isEmpty } from 'lodash';

const handleWarning = warning => {
	if (!isEmpty(warning.notify)) {
		notify({
			title: warning.notify,
			color: warning.color || 'orange',
			iconMain: warning.iconMain || 'warning'
		});
	}
};

export default handleWarning;
