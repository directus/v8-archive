/**
 * This transformation is used in the copy command for the meta.json files
 * It takes the contents of the json file, minifies it, and returns it.
 */

var through = require("through2");
var jsonminify = require("jsonminify");

module.exports = function(file) {
  return through(function(buf, enc, next) {
    this.push(jsonminify(buf.toString("utf8")));
    next();
  });
};
