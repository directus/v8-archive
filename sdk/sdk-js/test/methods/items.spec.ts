import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

describe("Items", () => {
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

  describe("#createItem()", () => {
    it("Calls post() for the right endpoint", () => {
      client.createItem("projects", { title: "Groetjes uit NYC" });
      expect(client.api.post).to.have.been.calledWith("/items/projects", {
        title: "Groetjes uit NYC",
      });
    });

    it("Calls post() for the system endpoint if a directus_* table is requested", () => {
      client.createItem("directus_users", { title: "Groetjes uit NYC" });
      expect(client.api.post).to.have.been.calledWith("/users", {
        title: "Groetjes uit NYC",
      });
    });
  });

  describe("#updateItems()", () => {
    it("Calls patch() for the right endpoint", () => {
      client.updateItems("projects", [{ id: 1, title: "A" }, { id: 2, title: "B" }]);
      expect(client.api.patch).to.have.been.calledWith("/items/projects", [
        { id: 1, title: "A" },
        { id: 2, title: "B" },
      ]);
    });

    it("Calls patch() for the system endpoint if a directus_* table is requested", () => {
      client.updateItems("directus_users", [{ id: 1, title: "A" }, { id: 2, title: "B" }]);
      expect(client.api.patch).to.have.been.calledWith("/users", [{ id: 1, title: "A" }, { id: 2, title: "B" }]);
    });
  });

  describe("#updateItem()", () => {
    it("Calls patch() for the right endpoint", () => {
      client.updateItem("projects", "15", { title: "Groetjes uit NYC" });
      expect(client.api.patch).to.have.been.calledWith("/items/projects/15", {
        title: "Groetjes uit NYC",
      });
    });

    it("Calls patch() for the system endpoint if a directus_* table is requested", () => {
      client.updateItem("directus_users", "15", {
        title: "Groetjes uit NYC",
      });
      expect(client.api.patch).to.have.been.calledWith("/users/15", {
        title: "Groetjes uit NYC",
      });
    });
  });

  describe("#getItems()", () => {
    it("Calls get() for the right endpoint", () => {
      client.getItems("projects", { limit: 50 });
      expect(client.api.get).to.have.been.calledWith("/items/projects", {
        limit: 50,
      });
    });

    it("Calls get() for the system endpoint if a directus_* table is requested", () => {
      client.getItems("directus_users", { limit: 50 });
      expect(client.api.get).to.have.been.calledWith("/users", { limit: 50 });
    });
  });

  describe("#getItem()", () => {
    it("Calls get() for the right endpoint", () => {
      client.getItem("projects", 15, { fields: ["title", "author"] });
      expect(client.api.get).to.have.been.calledWith("/items/projects/15", {
        fields: ["title", "author"],
      });
    });

    it("Calls get() for the system endpoint if a directus_* table is requested", () => {
      client.getItem("directus_users", 15, {
        fields: ["title", "author"],
      });
      expect(client.api.get).to.have.been.calledWith("/users/15", {
        fields: ["title", "author"],
      });
    });
  });

  describe("#deleteItem()", () => {
    it("Calls delete() for the right endpoint", () => {
      client.deleteItem("projects", 15);
      expect(client.api.delete).to.have.been.calledWith("/items/projects/15");
    });

    it("Calls delete() for the system endpoint if a directus_* table is requested", () => {
      client.deleteItem("directus_users", 15);
      expect(client.api.delete).to.have.been.calledWith("/users/15");
    });
  });

  describe("#deleteItems()", () => {
    it("Calls delete() for the right endpoint", () => {
      client.deleteItems("projects", [15, 21]);
      expect(client.api.delete).to.have.been.calledWith("/items/projects/15,21");
    });

    it("Calls delete() for the system endpoint if a directus_* table is requested", () => {
      client.deleteItems("directus_users", [15, 21]);
      expect(client.api.delete).to.have.been.calledWith("/users/15,21");
    });
  });
});
