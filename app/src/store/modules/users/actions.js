import api from '../../../api';
import { SET_USERS } from '../../mutation-types';

export function getUsers({ commit }) {
	return api
		.getUsers({
			fields: [
				'id',
				'first_name',
				'last_name',
				'title',
				'status',
				'timezone',
				'role.*',
				'avatar.*',
				'company'
			]
		})
		.then(res => res.data)
		.then(users => {
			commit(SET_USERS, users);
		});
}
