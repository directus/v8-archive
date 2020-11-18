import { DirectusService } from '../src/directus-service';

const host = `localhost`;
const port = 8080;

export default {
  url: (path = ''): string => `http://${host}:${port}${path}`,

  newDirectusService(): DirectusService {
    return new DirectusService({
      url: this.url(),
      auth: {
        email: 'admin@example.com',
        password: 'password',
      },
      project: '_',
    });
  },
};
