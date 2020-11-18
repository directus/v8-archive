import * as chai from "chai";
import * as jwt from "jsonwebtoken";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../src/index";
import { APIError } from "../src/API";

const expect = chai.expect;
chai.use(sinonChai);

describe("Request", () => {
  let client: SDK;

  beforeEach(() => {
    client = new SDK({
      url: "https://demo-api.getdirectus.com",
      project: "testProject",
      mode: "jwt",
    });
  });

  describe("#request()", () => {
    beforeEach(() => {
      sinon.stub(client.api.xhr, "request").resolves();
    });

    afterEach(() => {
      try {
        (client.api.xhr.request as any).restore();
      } catch (err) {
        // do nothing ...
      }
    });

    describe("Allows arrays and objects for data", () => {
      it("Does not error when body is an array or object", () => {
        expect(async () => {
          try {
            await client.api.request("post", "/items", {}, []);
          } catch (err) {
            // error allowed, it only will give us "Network Error" because we mock the calls
          }
        }).to.not.throw();
        expect(async () => {
          try {
            await client.api.request("post", "/items", {}, {});
          } catch (err) {
            // error allowed, it only will give us "Network Error" because we mock the calls
          }
        }).to.not.throw();
      });
    });

    it("Errors when there is no API URL set", () => {
      (client.config as any).internalConfiguration.url = undefined;
      expect(() => client.api.request("get", "/items")).to.throw();
    });

    it("Calls Axios with the right config", () => {
      (client.api.xhr.request as any).returns(
        Promise.resolve({
          response: {
            data: {
              error: {
                code: 1,
                message: "Not Found",
              },
            },
          },
        })
      );

      client.api.request("get", "/ping");

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        data: undefined,
        headers: { "X-Directus-Project": "testProject" },
        method: "get",
        params: {},
        url: "/ping",
      });
    });

    it("Calls Axios with the right config (body)", () => {
      (client.api.xhr.request as any).returns(
        Promise.resolve({
          response: {
            data: {
              error: {
                code: 1,
                message: "Not Found",
              },
            },
          },
        })
      );

      client.api.request(
        "post",
        "/utils/random_string",
        {},
        {
          testing: true,
        }
      );

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        data: {
          testing: true,
        },
        headers: { "X-Directus-Project": "testProject" },
        method: "post",
        params: {},
        url: "/utils/random_string",
      });
    });

    it("Calls Axios with the right config (params)", () => {
      (client.api.xhr.request as any).returns(
        Promise.resolve({
          response: {
            data: {
              error: {
                code: 1,
                message: "Not Found",
              },
            },
          },
        })
      );

      client.api.request("get", "/utils/random_string", { queryParam: true });

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        data: undefined,
        headers: { "X-Directus-Project": "testProject" },
        method: "get",
        params: {
          queryParam: true,
        },
        url: "/utils/random_string",
      });
    });

    it("Adds Bearer header if access token is set", () => {
      (client.api.xhr.request as any).returns(
        Promise.resolve({
          response: {
            data: {
              error: {
                code: 1,
                message: "Not Found",
              },
            },
          },
        })
      );

      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "1h",
        noTimestamp: true,
      });

      client.api.request("get", "/utils/random_string", { queryParam: true });

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        data: undefined,
        headers: {
          Authorization: `Bearer ${client.config.token}`,
          "X-Directus-Project": "testProject",
        },
        method: "get",
        params: {
          queryParam: true,
        },
        url: "/utils/random_string",
      });
    });

    it("Returns network error if the API did not respond", async () => {
      (client.api.xhr.request as any).returns(
        Promise.reject({
          request: {},
        })
      );

      let error: APIError;

      try {
        await client.api.request("get", "/ping");
      } catch (err) {
        error = err;
      }

      // @ts-ignore
      expect(error.code).to.equal("-1");
      // @ts-ignore
      expect(error.method).to.equal("GET");
      // @ts-ignore
      expect(error.url).to.equal("/ping");
      // @ts-ignore
      expect(Object.keys(error.params).length).to.equal(0);
      // @ts-ignore
      expect(`${error}`).to.be.equal("Directus call failed: GET /ping {} - Network error (code -1)");
    });

    it("Returns API error if available", async () => {
      (client.api.xhr.request as any).returns(
        Promise.reject({
          response: {
            data: {
              error: {
                code: 1,
                message: "Not Found",
              },
            },
          },
        })
      );

      let error: APIError;

      try {
        await client.api.request("get", "/ping");
      } catch (err) {
        error = err;
      }

      // @ts-ignore
      expect(error.code).to.equal("1");
      // @ts-ignore
      expect(error.message).to.equal("Not Found");
      // @ts-ignore
      expect(error.method).to.equal("GET");
      // @ts-ignore
      expect(error.url).to.equal("/ping");
      // @ts-ignore
      expect(Object.keys(error.params).length).to.equal(0);
      // @ts-ignore
      expect(`${error}`).to.equal("Directus call failed: GET /ping {} - Not Found (code 1)");
    });

    it("Strips out Axios metadata from response", async () => {
      (client.api.xhr.request as any).resolves({
        data: {
          data: {},
          meta: {},
        },
        request: {},
        status: 200,
      });

      const result = await client.api.request("get", "/ping", {}, {}, true);

      expect(result).to.deep.include({
        data: {},
        meta: {},
      });
    });

    it("Supports an optional fifth parameter to make the request without the env", async () => {
      (client.api.xhr.request as any).resolves({
        response: {
          data: {
            error: {
              code: 1,
              message: "Not Found",
            },
          },
        },
      });

      await client.api.request("get", "/interfaces", {}, {}, true);

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/",
        data: {},
        headers: { "X-Directus-Project": "testProject" },
        method: "get",
        params: {},
        url: "/interfaces",
      });

      await client.api.request("get", "/interfaces", {}, {}, true);

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/",
        data: {},
        headers: { "X-Directus-Project": "testProject" },
        method: "get",
        params: {},
        url: "/interfaces",
      });
    });
  });

  describe("#get()", () => {
    beforeEach(() => {
      sinon.stub(client.api, "request");
    });

    afterEach(() => {
      (client.api.request as any).restore();
    });

    it("Calls request() with the right parameters", () => {
      client.api.get("/items/projects", {
        limit: 20,
      });

      expect(client.api.request).to.have.been.calledWith("get", "/items/projects", {
        limit: 20,
      });
    });
  });

  describe("#post()", () => {
    beforeEach(() => {
      sinon.stub(client.api, "request");
    });

    afterEach(() => {
      (client.api.request as any).restore();
    });

    describe("Allows arrays and objects for body", () => {
      it("Does not error when body is an array or object", () => {
        expect(() => client.api.post("projects", [])).to.not.throw();
        expect(() => client.api.post("projects", {})).to.not.throw();
      });
    });

    it("Calls request() with the right parameters", () => {
      client.api.post("/items/projects", {
        title: "New Project",
      });

      expect(client.api.request).to.have.been.calledWith(
        "post",
        "/items/projects",
        {},
        {
          title: "New Project",
        }
      );
    });
  });

  describe("#patch()", () => {
    beforeEach(() => {
      sinon.stub(client.api, "request");
    });

    afterEach(() => {
      (client.api.request as any).restore();
    });

    describe("Allows arrays and objects for body", () => {
      it("Does not error when body is an array or object", () => {
        expect(() => client.api.patch("projects", [])).to.not.throw();
        expect(() => client.api.patch("projects", {})).to.not.throw();
      });
    });

    it("Calls request() with the right parameters", () => {
      client.api.patch("/items/projects/1", {
        title: "New Project",
      });

      expect(client.api.request).to.have.been.calledWith(
        "patch",
        "/items/projects/1",
        {},
        {
          title: "New Project",
        }
      );
    });
  });

  describe("#put()", () => {
    beforeEach(() => {
      sinon.stub(client.api, "request");
    });

    afterEach(() => {
      (client.api.request as any).restore();
    });

    describe("Allows arrays and objects for body", () => {
      it("Does not error when body is an array or object", () => {
        expect(() => client.api.put("projects", [])).to.not.throw();
        expect(() => client.api.put("projects", {})).to.not.throw();
      });
    });

    it("Calls request() with the right parameters", () => {
      client.api.put("/items/projects/1", {
        title: "New Project",
      });

      expect(client.api.request).to.have.been.calledWith(
        "put",
        "/items/projects/1",
        {},
        {
          title: "New Project",
        }
      );
    });
  });

  describe("#delete()", () => {
    beforeEach(() => {
      sinon.stub(client.api, "request");
    });

    afterEach(() => {
      (client.api.request as any).restore();
    });

    it("Calls request() with the right parameters", () => {
      client.api.delete("/items/projects/1");

      expect(client.api.request).to.have.been.calledWith("delete", "/items/projects/1");
    });
  });
});
