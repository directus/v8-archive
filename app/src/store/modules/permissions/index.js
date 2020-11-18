import * as actions from './actions';
import mutations from './mutations';
import { clone } from 'lodash';

export const initialState = {};

export default {
	actions,
	mutations,
	state: clone(initialState)
};
