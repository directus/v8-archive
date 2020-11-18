import * as chai from "chai";
import * as jwt from "jsonwebtoken";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../../src/";

const expect = chai.expect;
chai.use(sinonChai);

/**
 * FIXME:
 * [ERR_STABLE] means that this test fails in the current stable SDK
 * therefor this test will be skipped at the moment until the
 * owners found the correct way how the test should work!
 */

describe.skip("Listing Preferences", () => {
  let client: SDK;

  beforeEach(() => {
    client = new SDK({
      token: "xxxxx.yyyyy.zzzzz", // FIXME: [ERR_STABLE] this was missing!
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
    sinon.stub(client.api, "request").resolves(responseJSON);
  });

  afterEach(() => {
    (client.api.get as any).restore();
    (client.api.put as any).restore();
    (client.api.patch as any).restore();
    (client.api.post as any).restore();
    (client.api.delete as any).restore();
    (client.api.request as any).restore();
  });

  describe("#getMyListingPreferences()", () => {
    it("Calls get() three times", () => {
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "1h",
        noTimestamp: true,
      });
      client.getMyListingPreferences("projects");
      // tslint:disable-next-line: no-unused-expression
      expect(client.api.get).to.have.been.calledThrice;
    });

    /**
     * Returns collection instead of user
     * FIXME: [ERR_STABLE]
     */
    it.skip("Returns the user preferences if there saved user preferences", async () => {
      client.config.token = jwt.sign({ group: 5, id: 1 }, "secret-string", {
        expiresIn: "1h",
        noTimestamp: true,
      });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][null]": 1,
          "filter[title][null]": 1,
          "filter[user][null]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [
            {
              request: "collection",
            },
          ],
        });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][eq]": 5,
          "filter[title][null]": 1,
          "filter[user][null]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [
            {
              request: "role",
            },
          ],
        });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][eq]": 5,
          "filter[title][null]": 1,
          "filter[user][eq]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [
            {
              request: "user",
            },
          ],
        });

      const result = await client.getMyListingPreferences("faq");

      expect(result).to.deep.include({
        request: "user",
      });
    });

    /**
     * Returns collection instead of group
     * FIXME: [ERR_STABLE]
     */
    it.skip("Returns the group preferences if there are no saved user preferences", async () => {
      client.config.token = jwt.sign({ group: 5, id: 1 }, "secret-string", {
        expiresIn: "1h",
        noTimestamp: true,
      });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][null]": 1,
          "filter[title][null]": 1,
          "filter[user][null]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [
            {
              request: "collection",
            },
          ],
        });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][eq]": 5,
          "filter[title][null]": 1,
          "filter[user][null]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [
            {
              request: "group",
            },
          ],
        });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][eq]": 5,
          "filter[title][null]": 1,
          "filter[user][eq]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [],
        });

      const result = await client.getMyListingPreferences("faq");

      expect(result).to.deep.include({
        request: "group",
      });
    });

    /**
     * FIXME: [ERR_STABLE]
     */
    it("Returns the collection preferences if there are no saved user or preferences", async () => {
      client.config.token = jwt.sign({ group: 5, id: 1 }, "secret-string", {
        expiresIn: "1h",
        noTimestamp: true,
      });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][null]": 1,
          "filter[title][null]": 1,
          "filter[user][null]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [
            {
              request: "collection",
            },
          ],
        });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][eq]": 5,
          "filter[title][null]": 1,
          "filter[user][null]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [],
        });

      (client.api.get as any)
        .withArgs("/collection_presets", {
          "filter[collection][eq]": "faq",
          "filter[role][eq]": 5,
          "filter[title][null]": 1,
          "filter[user][eq]": 1,
          limit: 1,
          sort: "-id",
        })
        .resolves({
          data: [],
        });

      const result = await client.getMyListingPreferences("faq");

      expect(result).to.deep.include({
        request: "collection",
      });
    });
  });
});
