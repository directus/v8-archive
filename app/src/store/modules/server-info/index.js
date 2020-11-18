import mutations from './mutations';
import * as actions from './actions';
import { clone } from 'lodash';

export const initialState = {
	apiVersion: null,
	phpVersion: null,
	maxUploadSize: null,

	// This should be dynamic as soon as the API supports multiple database vendors
	databaseVendor: 'mysql'
};

export default {
	state: clone(initialState),
	mutations,
	actions
};
