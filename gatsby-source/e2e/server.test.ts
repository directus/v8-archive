import TestServer from './test-server';
import axios from 'axios';

describe('Server', () => {
  it('should reponse to ping', async () => {
    const res = await axios.get(TestServer.url('/server/ping'));
    expect(res.data).toMatch(/.*pong.*/);
  });
});
