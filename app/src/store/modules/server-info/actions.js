import api from '@/api';
import { SET_SERVER_INFO } from '@/store/mutation-types';
import { get } from 'lodash';

export function getServerInfo({ commit }) {
	return api
		.projectInfo()
		.then(res => res.data)
		.then(info => {
			commit(SET_SERVER_INFO, {
				apiVersion: get(info, 'api.version'),
				phpVersion: get(info, 'server.general.php_version'),
				maxUploadSize: get(info, 'server.max_upload_size')
			});
		});
}
