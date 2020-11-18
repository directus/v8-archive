/**
 * CLOUD
 *
 * Checks if a passed project key is a valid Directus Cloud project key
 */
export default function isCloudProject(projectKey) {
	return (
		typeof projectKey === 'string' &&
		projectKey.startsWith('dc') &&
		projectKey.length === 16 &&
		/dc[A-Za-z0-9]{14}/.test(projectKey)
	);
}
