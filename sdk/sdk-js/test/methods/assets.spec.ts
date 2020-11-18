import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

import { mockAxiosResponse } from "../mock/response";

const expect = chai.expect;
chai.use(sinonChai);

describe("Assets", () => {
  let client: SDK;

  beforeEach(() => {
    client = new SDK({
      token: "abcdef",
      url: "https://demo-api.getdirectus.com",
      project: "testProject",
      mode: "jwt",
    });
  });

  describe("#getAssetUrl()", () => {
    it("Return an original's URL when only passed the private hash", async () => {
      const url = client.getAssetUrl("xxxxxxxx-xxxxxxxx");

      expect(url).to.equal(
        "https://demo-api.getdirectus.com/testProject/assets/xxxxxxxx-xxxxxxxx"
      );
    });

    it("Return an original's URL when passed an empty params object", async () => {
      const url = client.getAssetUrl("xxxxxxxx-xxxxxxxx", {});

      expect(url).to.equal(
        "https://demo-api.getdirectus.com/testProject/assets/xxxxxxxx-xxxxxxxx"
      );
    });

    it("Return a thumbnail's URL when passed a thumbnail key", async () => {
      const url = client.getAssetUrl("xxxxxxxx-xxxxxxxx", {
        key: "thumbnail"
      });

      expect(url).to.equal(
        "https://demo-api.getdirectus.com/testProject/assets/xxxxxxxx-xxxxxxxx" +
        "?key=thumbnail"
      );
    });

    it("Return a thumbnail's URL when passed thumbnail specs", async () => {
      const url = client.getAssetUrl("xxxxxxxx-xxxxxxxx", {
        w: 100,
        h: 75,
        f: "crop",
        q: 80,
      });

      expect(url).to.equal(
        "https://demo-api.getdirectus.com/testProject/assets/xxxxxxxx-xxxxxxxx" +
        "?w=100&h=75&f=crop&q=80"
      );
    });
  });

  describe("#getAsset()", () => {
    beforeEach(() => {
      const responseArrayBuffer = mockAxiosResponse<ArrayBuffer>(new ArrayBuffer(0));
      sinon.stub(client.api.xhr, "request").resolves(responseArrayBuffer);
    });

    afterEach(() => {
      (client.api.xhr.request as any).restore();
    });

    it("Return a thumbnail when passed a thumbnail key", async () => {
      const params = {
        key: "thumbnail"
      };
      await client.getAsset("xxxxxxxx-xxxxxxxx", params);

      expect(client.api.xhr.request).to.have.been.calledWith(sinon.match({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        url: "/assets/xxxxxxxx-xxxxxxxx",
        params: sinon.match(params),
      }));
    });

    it("Return a thumbnail when passed thumbnail specs", async () => {
      const params = {
        w: 100,
        h: 75,
        f: "crop",
        q: 80,
      };
      await client.getAsset("xxxxxxxx-xxxxxxxx", params);

      expect(client.api.xhr.request).to.have.been.calledWith(sinon.match({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        url: "/assets/xxxxxxxx-xxxxxxxx",
        params: sinon.match(params),
      }));
    });

    it("Restores the axios responseType after running the query", async () => {
      const previousResponseType = client.api.xhr.defaults.responseType;
      const params = { key: "thumbnail" };

      await client.getAsset("xxxxxxxx-xxxxxxxx", params);

      expect(client.api.xhr.request).to.have.been.called;
      expect(client.api.xhr.defaults.responseType).to.equal(previousResponseType);
    });
  });
});
