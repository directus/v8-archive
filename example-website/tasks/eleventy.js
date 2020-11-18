const exec = require('child_process').exec;

function eleventy(callback) {
  const command = 'eleventy';

  exec(command, function (error, stdout, stderr) {
    console.log(stdout);
    console.log(stderr);
    callback(error);
  });
}

module.exports = eleventy;
