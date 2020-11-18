import * as chai from "chai";
import { querify } from "../../src/utils/qs";

const expect = chai.expect;

describe("QS", () => {
  describe("#querify()", () => {
    it("Serialize single parameters", () => {
      const qs = querify({ field1: "12345" });
      expect(qs).to.be.equal("field1=12345");
    });

    it("Serialize multiple parameters", () => {
      const qs = querify({ field1: "12345", field2: 54321 });
      expect(qs).to.be.equal("field1=12345&field2=54321");
    });

    it("Serialize array parameters", () => {
      const qs = querify({ field: ["1", "2"] });
      expect(qs).to.be.equal("field[0]=1&field[1]=2");
    });

    it("Serialize complex parameters", () => {
      const qs = querify({ parent: { child: { value: 1 } } });
      expect(qs).to.be.equal("parent[child][value]=1");
    });

    it("Serialize parameters that needs encoding", () => {
      const qs = querify({ name: "ben & jerry" });
      expect(qs).to.be.equal("name=ben%20%26%20jerry");
    });
  });
});
