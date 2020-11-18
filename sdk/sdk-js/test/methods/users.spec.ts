import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

describe("Users", () => {
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
    sinon.stub(client, "updateItem").resolves(responseJSON as any);
  });

  afterEach(() => {
    (client.api.get as any).restore();
    (client.api.put as any).restore();
    (client.api.patch as any).restore();
    (client.api.post as any).restore();
    (client.api.delete as any).restore();
    (client.updateItem as any).restore();
  });

  describe("#getUsers()", () => {
    it("Defaults to an empty object if no parameters are passed", () => {
      client.getUsers();
      expect(client.api.get).to.have.been.calledWith("/users", {});
    });

    it("Calls get() for the right endpoint", () => {
      client.getUsers({ limit: 50 });
      expect(client.api.get).to.have.been.calledWith("/users", { limit: 50 });
    });
  });

  describe("#getUser()", () => {
    it("Calls get() for the right endpoint", () => {
      client.getUser(15, { fields: "first_name" });
      expect(client.api.get).to.have.been.calledWith("/users/15", {
        fields: "first_name",
      });
    });
  });

  describe("#getMe()", () => {
    it("Defaults to an empty object if no parameters are passed", () => {
      client.getMe();
      expect(client.api.get).to.have.been.calledWith("/users/me", {});
    });

    it("Calls get() for the right endpoint", () => {
      client.getMe({ fields: "first_name" });
      expect(client.api.get).to.have.been.calledWith("/users/me", {
        fields: "first_name",
      });
    });
  });

  describe("#createUser()", () => {
    it("Calls post() for the right endpoint", () => {
      client.createUser({
        first_name: "Ben",
        last_name: "Haynes",
        email: "demo@example.com",
        password: "d1r3ctu5",
        role: 3,
        status: "active"
      });
      expect(client.api.post).to.have.been.calledWith("/users", {
        first_name: "Ben",
        last_name: "Haynes",
        email: "demo@example.com",
        password: "d1r3ctu5",
        role: 3,
        status: "active"
      });
    });
  });

  describe("#updateUser()", () => {
    it("Calls #updateItem()", () => {
      client.updateUser(15, { last_page: "/activity" });
      expect(client.updateItem).to.have.been.calledWith("directus_users", 15, {
        last_page: "/activity",
      });
    });
  });
});
