import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

describe("Revisions", () => {
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

  describe("#getItemRevisions()", () => {
    it("Calls get() for the right endpoint", () => {
      client.getItemRevisions("projects", 15, {
        fields: ["title", "author"],
      });
      expect(client.api.get).to.have.been.calledWith("/items/projects/15/revisions", { fields: ["title", "author"] });
    });

    it("Calls get() for the system endpoint if a directus_* table is requested", () => {
      client.getItemRevisions("directus_users", 15, {
        fields: ["title", "author"],
      });
      expect(client.api.get).to.have.been.calledWith("/users/15/revisions", {
        fields: ["title", "author"],
      });
    });
  });

  describe("#revert()", () => {
    it("Calls patch() for the right endpoint", () => {
      client.revert("projects", 15, 130);
      expect(client.api.patch).to.have.been.calledWith("/items/projects/15/revert/130");
    });

    it("Calls patch() for the system endpoint if a directus_* table is requested", () => {
      client.revert("directus_users", 15, 130);
      expect(client.api.patch).to.have.been.calledWith("/users/15/revert/130");
    });
  });
});
