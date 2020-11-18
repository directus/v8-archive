import * as chai from "chai";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";
import axios from "axios";

import { mockAxiosResponse } from "../mock/response";

const expect = chai.expect;
chai.use(sinonChai);

describe("Files", () => {
  let client: SDK;

  beforeEach(() => {
    client = new SDK({
      token: "abcdef",
      url: "https://demo-api.getdirectus.com",
      project: "testProject",
      mode: "jwt",
    });

    const responseJSON = mockAxiosResponse({
      data: {
        data: {},
      },
    });

    sinon.stub(client.api.xhr, "post").resolves(responseJSON);
    sinon.stub(client.api, "post").resolves(responseJSON);
    sinon.stub(axios, "post").resolves(mockAxiosResponse);
  });

  afterEach(() => {
    (client.api.xhr.post as any).restore();
    (client.api.post as any).restore();
  });

  describe("#uploadFiles()", () => {
    it("Calls post() for the right endpoint", async () => {
      await client.uploadFiles(["fileA", "fileB"]);

      // Validate against upload post parameters
      expect(client.api.post).to.have.been.calledWith("/files");
    });
  });
});
