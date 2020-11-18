import Vue from 'vue';

/**
 * Check if component is registered before. Can be used to prevent registering components that have
 *   been registered before
 * @param  {String} name The name of the Vue component
 * @return {Boolean}     Exists or not
 */
export default function componentExists(name) {
	return Vue.options.components[name] && Object.keys(Vue.options.components[name]).length > 0;
}
