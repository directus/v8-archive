// tslint:disable
import * as chai from "chai";
import * as sinonChai from "sinon-chai";
import SDK from "../src/index";

const expect = chai.expect;
chai.use(sinonChai);

describe("General", () => {
  it("Creates a new instance without errors", () => {
    expect(() => new SDK(undefined as any)).not.to.throw();
  });

  it("Allows you to set and retrieve the api url", () => {
    const client = new SDK(undefined as any);
    client.config.url = "https://demo-api.getdirectus.com/";
    expect(client.config.url).to.equal("https://demo-api.getdirectus.com/");
  });

  it("Allows you to set the url on creation", () => {
    const client = new SDK({
      url: "https://demo-api.getdirectus.com/",
      project: "testProject",
      mode: "jwt",
    }) as any;
    expect(client.config.url).to.equal("https://demo-api.getdirectus.com/");
  });

  it("Allows you to set and retrieve the access token", () => {
    const client = new SDK(undefined as any);
    client.config.token = "abcdef123456";
    expect(client.config.token).to.equal("abcdef123456");
  });

  it("Allows you to set the access token on creation", () => {
    const client = new SDK({
      token: "abcdef123456",
    } as any);
    expect(client.config.token).to.equal("abcdef123456");
  });

  it("Allows you to set and retrieve the project in use", () => {
    const client = new SDK(undefined as any) as any;
    client.config.project = "staging";
    expect(client.config.project).to.equal("staging");
  });

  it("Allows you to set the project on creation", () => {
    const client = new SDK({
      project: "staging",
    } as any) as any;
    expect(client.config.project).to.equal("staging");
  });

  it("Defaults the project to underscore (_)", () => {
    const client = new SDK(undefined as any) as any;
    expect(client.config.project).to.be.undefined;
  });
});
