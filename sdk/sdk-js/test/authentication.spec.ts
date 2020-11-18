import * as chai from "chai";
import * as jwt from "jsonwebtoken";
import * as sinon from "sinon";
import * as sinonChai from "sinon-chai";
import SDK from "../src/index";
import { IAuthenticationRefreshError } from "../src/Authentication";
import { IConfigurationValues } from "../src/Configuration";

const expect = chai.expect;
chai.use(sinonChai);

describe("Authentication", () => {
  let client: SDK;

  beforeEach(() => {
    client = new SDK({
      url: "https://demo-api.getdirectus.com",
      project: "testProject",
      mode: "jwt",
    });

    client.config.reset();
    client.config.url = "https://demo-api.getdirectus.com";

    sinon.stub(client.api.xhr, "request").resolves({
      config: {},
      data: {
        data: {
          token: "abcdef",
        },
      },
      headers: {},
      status: 200,
      statusText: "OK",
    });
  });

  afterEach(() => {
    (client.api.xhr.request as any).restore();
  });

  describe("#login()", () => {
    it("Sets the url in use when passed in credentials", async () => {
      await client.login({
        email: "test@example.com",
        password: "testPassword",
        project: "testProject",
        url: "https://demo-api.getdirectus.com",
      });

      expect(client.config.url).to.equal("https://demo-api.getdirectus.com");
    });

    it("Calls Axios with the right parameters", async () => {
      await client.login({
        email: "test@example.com",
        password: "testPassword",
        project: "testProject",
        url: "https://demo-api.getdirectus.com",
      });

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        data: {
          email: "test@example.com",
          password: "testPassword",
          mode: "jwt",
        },
        headers: {
          "X-Directus-Project": "testProject",
        },
        method: "post",
        params: {},
        url: "/auth/authenticate",
      });
    });

    it("Replaces the stored token", async () => {
      await client.login({
        email: "text@example.com",
        password: "testPassword",
        project: "testProject",
        url: "https://demo-api.getdirectus.com",
      });

      expect(client.config.token).to.equal("abcdef");
    });

    it("Replaces project and url if passed", async () => {
      await client.login({
        email: "text@example.com",
        password: "testPassword",
        project: "testProject",
        url: "https://example.com",
      });

      expect(client.config.url).to.equal("https://example.com");
      expect(client.config.project).to.equal("testProject");
    });

    it("Resolves with the currently logged in token, url, and project", async () => {
      const result = await client.login({
        email: "text@example.com",
        password: "testPassword",
        project: "testProject",
        url: "https://example.com",
      });

      expect(result).to.deep.equal({ data: { token: "abcdef" } });
    });
  });

  describe("#logout()", () => {
    it("Calls the right API logout endpoint", async () => {
      client.config.project = "testProject";

      await client.logout();

      expect(client.api.xhr.request).to.have.been.calledWith({
        baseURL: "https://demo-api.getdirectus.com/testProject/",
        data: {},
        headers: {
          "X-Directus-Project": "testProject",
        },
        method: "post",
        params: {},
        url: "/auth/logout",
      });
    });

    it("Nullifies the token", async () => {
      client.config.project = "testProject";

      await client.logout();

      expect(client.config.token).to.be.undefined;
    });
  });

  describe("#refresh()", () => {
    it("Resolves with the new token", async () => {
      client.config.project = "testProject";
      const result = await client.refresh("oldToken");
      expect(result).to.deep.include({
        data: {
          token: "abcdef",
        },
      });
    });
  });

  describe("#refreshIfNeeded()", () => {
    it("Does nothing when token, url, project, or payload.exp is missing", () => {
      // Nothing
      client.config.token = undefined;
      expect(client.refreshIfNeeded()).to.be.undefined;
      // URL
      client.config.url = "https://demo-api.getdirectus.com";
      expect(client.refreshIfNeeded()).to.be.undefined;
      // URL + ENV
      client.config.project = "_";
      expect(client.refreshIfNeeded()).to.be.undefined;
      // URL + ENV + TOKEN (no exp in payload)
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        noTimestamp: true,
      });
      expect(client.refreshIfNeeded()).to.be.undefined;
    });

    it("Overwrites the saved token with the new one", async () => {
      sinon.stub(client.api.auth, "refresh").resolves({
        data: {
          token: "abcdef",
        },
        meta: {},
      });
      client.config.url = "https://example.com";
      client.config.project = "testProject";
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "20s",
        noTimestamp: true,
      });
      client.config.localExp = Date.now() + 10e2;

      await client.refreshIfNeeded();

      expect(client.config.token).to.equal("abcdef");
      (client.api.auth.refresh as any).restore();
    });

    it("Calls refresh() if expiry date is within 30 seconds of now", async () => {
      sinon.stub(client.api.auth, "refresh").resolves();
      client.config.url = "https://example.com";
      client.config.project = "testProject";

      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "1h",
        noTimestamp: true,
      });
      await client.api.auth.refreshIfNeeded();
      expect(client.api.auth.refreshIfNeeded()).to.be.undefined;
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "20s",
        noTimestamp: true,
      });
      client.config.localExp = Date.now() + 10;
      await client.api.auth.refreshIfNeeded();
      expect(client.api.auth.refresh).to.have.been.calledWith(client.config.token);
      (client.api.auth.refresh as any).restore();
    });

    it("Calls the optional onAutoRefreshSuccess() callback when the request succeeds", done => {
      sinon.stub(client.api.auth, "refresh").resolves({
        data: {
          token: "abcdef",
        },
        meta: {},
      });

      client.config.url = "https://demo-api.getdirectus.com";
      client.config.project = "testProject";
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "15s",
        noTimestamp: true,
      });

      (client.api.auth as any).onAutoRefreshSuccess = (info: IConfigurationValues) => {
        expect(info).to.deep.include({
          project: "testProject",
          token: "abcdef",
          url: "https://demo-api.getdirectus.com",
        });
        (client.api.auth.refresh as any).restore();
        done();
      };

      client.config.localExp = Date.now() + 10;
      client.refreshIfNeeded();
    });

    it("Calls the optional onAutoRefreshError() callback when request fails", done => {
      sinon.stub(client.api.auth, "refresh").rejects({
        code: -1,
        message: "Network Error",
      });

      client.config.url = "https://example.com";
      client.config.project = "testProject";
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "20s",
        noTimestamp: true,
      });
      client.config.localExp = Date.now() + 10;

      (client.api.auth as any).onAutoRefreshError = (error: IAuthenticationRefreshError) => {
        expect(error).to.deep.include({
          code: -1,
          message: "Network Error",
        });
        done();
      };

      client.refreshIfNeeded();

      (client.api.auth.refresh as any).restore();
    });

    it("Does nothing if the token is expired and no onAutoRefreshError() callback has been given", () => {
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "-20s",
        noTimestamp: true,
      });
      expect(client.refreshIfNeeded()).to.be.undefined;
    });

    it("Calls the optional onAutoRefreshError() callback when trying to refresh an expired token", done => {
      sinon.stub(client.api.auth, "refresh").rejects({});

      client.config.url = "https://example.com";
      client.config.project = "testProject";
      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "-20s",
        noTimestamp: true,
      });

      (client.api.auth as any).onAutoRefreshError = (error: IAuthenticationRefreshError) => {
        expect(error).to.deep.include({
          code: 102,
          message: "auth_expired_token",
        });
        done();
      };

      client.refreshIfNeeded();

      (client.api.auth.refresh as any).restore();
    });
  });

  describe("Interval", () => {
    beforeEach(() => {
      // @ts-ignore
      this.clock = sinon.useFakeTimers();
      sinon.stub(client.api.auth, "refreshIfNeeded");
    });

    afterEach(() => {
      // @ts-ignore
      this.clock.restore();
      (client.api.auth as any).refreshIfNeeded.restore();
    });

    describe("#startInterval()", () => {
      it("Starts the interval", () => {
        // startInterval() is private
        (client.api.auth as any).startInterval();
        expect(client.api.auth.refreshInterval).to.be.not.null;
      });

      it("Fires immediately if true has been passed as parameter", () => {
        // startInterval() is private
        (client.api.auth as any).startInterval(true);
        expect(client.api.auth.refreshIfNeeded).to.have.been.calledOnce;
      });
    });

    describe("#stopInterval()", () => {
      it("Stops (deletes) the interval", () => {
        // startInterval() and stopInterval() are private
        (client.api.auth as any).startInterval();
        (client.api.auth as any).stopInterval();
        expect(client.api.auth.refreshInterval).to.be.undefined;
      });
    });

    describe("#login()", () => {
      it("Starts the interval if persist key has been passed", () => {
        client.login({
          email: "testing@example.com",
          password: "testPassword",
          persist: true,
          project: "testProject",
          url: "https://demo-api.getdirectus.com",
        });

        expect(client.api.auth.refreshInterval).to.be.not.null;

        // cleanup
        client.logout();
      });

      it("Does not start the interval without the persist key", async () => {
        client.config.project = "testProject";

        await client.logout();
        client.config.reset();

        await client.login({
          email: "testing@example.com",
          password: "testPassword",
          url: "https://demo-api.getdirectus.com",
          project: "testProject",
        });

        expect(client.api.auth.refreshInterval).to.be.undefined;
      });
    });

    describe("#logout()", () => {
      it("Removes any interval on logout", async () => {
        client.login({
          email: "testing@example.com",
          password: "testPassword",
          persist: true,
          project: "testProject",
          url: "https://demo-api.getdirectus.com",
        });

        await client.logout();

        expect(client.api.auth.refreshInterval).to.be.undefined;
      });
    });

    describe("#requestPasswordReset()", () => {
      beforeEach(() => {
        sinon.stub(client.api, "post");
      });

      afterEach(() => {
        (client.api.post as any).restore();
      });

      it("Errors when email parameter is missing", () => {
        expect(client.requestPasswordReset).to.throw();
      });

      it("Calls post sending the required body", async () => {
        await client.requestPasswordReset("test@example.com");

        expect(client.api.post).to.have.been.calledWith("/auth/password/request", {
          email: "test@example.com",
        });
      });
    });

    it("Fires refreshIfNeeded() every 10 seconds", () => {
      client.login({
        email: "testing@example.com",
        password: "testPassword",
        persist: true,
        url: "https://demo-api.getdirectus.com",
        project: "testProject",
      });
      expect(client.api.auth.refreshIfNeeded).to.have.not.been.called;

      client.config.token = jwt.sign({ foo: "bar" }, "secret-string", {
        expiresIn: "20s",
        noTimestamp: true,
      });

      // @ts-ignore
      this.clock.tick(11000);
      expect(client.api.auth.refreshIfNeeded).to.have.been.calledOnce;

      // @ts-ignore
      this.clock.tick(11000);
      expect(client.api.auth.refreshIfNeeded).to.have.been.calledTwice;
    });
  });
});
