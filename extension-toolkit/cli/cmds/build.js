const fs = require("fs");
const Bundler = require("parcel-bundler");
const path = require("path");
const jsonminify = require("jsonminify");

exports.command = "build [--no-source-maps] [input] [output]";
exports.desc = "Build the source files for production use";
exports.builder = yargs => {
  yargs.option('source-maps', {
    boolean: true,
    default: true,
    describe: 'Skip source maps generation'
  });
  yargs.option('cache-dir', {
    default: '.cache',
  });
  return yargs;
};
exports.handler = async function(argv) {
  let { input, output, cacheDir, sourceMaps } = argv;
  input = input || "src";
  output = output || "dist";

  const entryFiles = path.join(input, "*.vue");

  const bundler = new Bundler(entryFiles, {
    outDir: output,
    watch: false,
    minify: true,
    global: "__DirectusExtension__",
    cacheDir,
    sourceMaps,
    publicUrl: './'
  });

  await bundler.bundle();

  const metaFile = fs.readFileSync(path.join(input, "meta.json"), "utf8");
  fs.writeFileSync(path.join(output, "meta.json"), jsonminify(metaFile));
}
