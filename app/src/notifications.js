import store from './store';
import { PUSH_NOTIFICATION } from './store/mutation-types';
import shortid from 'shortid';

const defaultOptions = {
	delay: 5000
};

const notify = function(params) {
	const options = {
		...defaultOptions,
		...params,
		id: shortid.generate()
	};
	store.commit(PUSH_NOTIFICATION, options);
};

export default notify;
