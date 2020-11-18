const fs = require("fs");
const { exec } = require("child_process");
const path = require("path");
const ejs = require("ejs");

exports.command = "create [type] [name]";
exports.desc = "Create a new custom extension";
exports.handler = function(argv) {
  const { type, name } = argv;

  const allowedTypes = ["interface", "layout", "module"];

  if (allowedTypes.includes(type) === false)
    return console.log("Invalid type provided. Allowed types: " + allowedTypes.join(", "));

  try {
    fs.mkdirSync(name);
  } catch(error) {
    if (error.code === "EEXIST") {
      console.log("Folder with the name " + name + " already exists.");
      return;
    }
  }

  fs.mkdirSync(path.join(name, "src"));

  const files = {
    all: ["package.json", "readme.md", "gitignore"],
    interface: ["input.vue", "display.vue", "meta.json"],
    layout: ["layout.vue", "options.vue", "meta.json"],
    module: ["module.vue", "meta.json"]
  };

  files.all.forEach(file => parseTemplate(file, false));
  files[type].forEach(file => parseTemplate(file));

  exec(`cd ${name}; npm install;`);

  function parseTemplate(file, src = true) {
    const template = fs.readFileSync(path.join(__dirname, "../templates", file), "utf8");
    const fileContent = ejs.render(template, { name });
    if (file === "gitignore") file = ".gitignore";
    fs.writeFileSync(path.join(name, src ? "src" : "", file), fileContent);
  }
}
