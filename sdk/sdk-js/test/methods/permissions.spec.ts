import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

describe("Permissions", () => {
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

    sinon.stub(client.api, "get").resolves(responseJSON);
    sinon.stub(client.api, "put").resolves(responseJSON);
    sinon.stub(client.api, "patch").resolves(responseJSON);
    sinon.stub(client.api, "post").resolves(responseJSON);
    sinon.stub(client.api, "delete").resolves(responseJSON);
  });

  afterEach(() => {
    (client.api.get as any).restore();
    (client.api.put as any).restore();
    (client.api.patch as any).restore();
    (client.api.post as any).restore();
    (client.api.delete as any).restore();
  });

  describe("#getPermissions()", () => {
    it("Defaults to an empty object if no parameters are passed", () => {
      client.getPermissions();
      expect(client.api.get).to.have.been.calledWith("/permissions", {});
    });

    it("Calls get() for the right endpoint", () => {
      client.getPermissions({ limit: 50 });
      expect(client.api.get).to.have.been.calledWith("/permissions", {
        limit: 50,
      });
    });
  });

  describe("#updatePermissions()", () => {
    it("Calls post() for the right endpoint", () => {
      client.createPermissions([{ read: "none", collection: "projects" }]);
      expect(client.api.post).to.have.been.calledWith("/permissions", [{ read: "none", collection: "projects" }]);
    });
  });

  describe("#updatePermissions()", () => {
    it("Calls post() for the right endpoint", () => {
      client.updatePermissions([{ read: "none", collection: "projects" }]);
      expect(client.api.patch).to.have.been.calledWith("/permissions", [{ read: "none", collection: "projects" }]);
    });
  });
});
