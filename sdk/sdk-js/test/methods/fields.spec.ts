import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

describe("Fields", () => {
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

  describe("#getAllFields()", () => {
    it("Defaults to an empty object if no parameters are passed", () => {
      client.getAllFields();
      expect(client.api.get).to.have.been.calledWith("/fields", {});
    });

    it("Calls get() for the right endpoint", () => {
      client.getAllFields({ limit: 50 });
      expect(client.api.get).to.have.been.calledWith("/fields", {
        limit: 50,
      });
    });
  });

  describe("#getFields()", () => {
    it("Calls get() for the right endpoint", () => {
      client.getFields("projects", { limit: 50 });
      expect(client.api.get).to.have.been.calledWith("/fields/projects", {
        limit: 50,
      });
    });
  });

  describe("#getField()", () => {
    it("Calls get() for the right endpoint", () => {
      client.getField("projects", "title", { fields: "interface" });
      expect(client.api.get).to.have.been.calledWith("/fields/projects/title", {
        fields: "interface",
      });
    });
  });

  describe("#createField()", () => {
    it("Calls post() for the right endpoint", () => {
      // @ts-ignore
      client.createField("members", {
        field: "first_name",
        interface: "text-input",
      });
      expect(client.api.post).to.have.been.calledWith("/fields/members", {
        field: "first_name",
        interface: "text-input",
      });
    });
  });

  describe("#updateField()", () => {
    it("Calls patch() for the right endpoint", () => {
      client.updateField("members", "first_name", {
        field: "first_name",
        interface: "text-input",
      });
      expect(client.api.patch).to.have.been.calledWith("/fields/members/first_name", {
        field: "first_name",
        interface: "text-input",
      });
    });
  });

  describe("#updateFields", () => {
    it("Calls patch() multiple fields same value", () => {
      client.updateFields("members", ["first_name", "last_name"], {
        default_value: "",
      });

      expect(client.api.patch).to.have.been.calledWith("/fields/members/first_name,last_name", {
        default_value: "",
      });
    });

    it("Calls patch() multiple fields multiple values", () => {
      client.updateFields("members", [
        {
          field: "id",
          sort: 1,
        },
        {
          field: "first_name",
          sort: 2,
        },
      ]);

      expect(client.api.patch).to.have.been.calledWith("/fields/members", [
        {
          field: "id",
          sort: 1,
        },
        {
          field: "first_name",
          sort: 2,
        },
      ]);
    });
  });

  describe("#deleteField()", () => {
    it("Calls delete() for the right endpoint", () => {
      client.deleteField("test", "field");
      expect(client.api.delete).to.have.been.calledWith("/fields/test/field");
    });
  });
});
