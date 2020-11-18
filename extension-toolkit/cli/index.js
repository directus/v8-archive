#!/usr/bin/env node

const argv = require("yargs")
  .commandDir("./cmds")
  .demandCommand()
  .help()
  .epilogue("✨🐰✨")
  .argv;
