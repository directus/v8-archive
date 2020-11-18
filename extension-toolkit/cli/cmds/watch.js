const fs = require("fs");
const Bundler = require("parcel-bundler");
const path = require("path");
const jsonminify = require("jsonminify");

exports.command = "watch [--no-source-maps] [input|--input] [output|--output]";

exports.desc = "Watch the source files for changes and rebuild";
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
    watch: true,
    minify: false,
    global: "__DirectusExtension__",
    cacheDir,
    sourceMaps,
    publicUrl: './'
  });

  await bundler.bundle();

  const metaFile = fs.readFileSync(path.join(input, "meta.json"), "utf8");
  fs.writeFileSync(path.join(output, "meta.json"), jsonminify(metaFile));
}
