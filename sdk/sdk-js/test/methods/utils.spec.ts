import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

describe("Utils", () => {
  let client: SDK;

  beforeEach(() => {
    client = new SDK({
      url: "https://demo-api.getdirectus.com",
      project: "testProject",
      mode: "jwt",
    });

    const responseJSON = {
      data: {
        data: {},
      },
    };

    sinon.stub(client.api, "request").resolves(responseJSON);
    sinon.stub(client.api, "get").resolves(responseJSON);
    sinon.stub(client.api, "put").resolves(responseJSON);
    sinon.stub(client.api, "patch").resolves(responseJSON);
    sinon.stub(client.api, "post").resolves(responseJSON);
    sinon.stub(client.api, "delete").resolves(responseJSON);
  });

  afterEach(() => {
    (client.api.request as any).restore();
    (client.api.get as any).restore();
    (client.api.put as any).restore();
    (client.api.patch as any).restore();
    (client.api.post as any).restore();
    (client.api.delete as any).restore();
  });

  describe("#ping()", () => {
    it("It calls get for the ping endpoint", () => {
      client.ping();
      expect(client.api.request).to.have.been.calledWith("get", "/server/ping", {}, {}, true);
    });
  });

  describe("#getThirdPartyAuthProviders()", () => {
    it("It calls get for the sso endpoint", () => {
      client.getThirdPartyAuthProviders();
      expect(client.api.get).to.have.been.calledWith("/auth/sso");
    });
  });
});
