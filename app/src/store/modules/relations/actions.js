import api from '../../../api';
import { SET_RELATIONS, ADD_RELATION, UPDATE_RELATION } from '../../mutation-types';

export function getRelations({ commit }) {
	return api
		.getRelations({ limit: -1 })
		.then(res => res.data)
		.then(relations => commit(SET_RELATIONS, relations));
}

export function addRelation({ commit }, relation) {
	commit(ADD_RELATION, relation);
}

export function updateRelation({ commit }, relation) {
	commit(UPDATE_RELATION, relation);
}
