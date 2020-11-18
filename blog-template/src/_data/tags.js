const path = require("path");
const flatcache = require("flat-cache");
const sdk = require("@directus/sdk-js");

// Create a new instance of the Directus Javascript SDK
const directus = new sdk({
  url: "https://api.directus.cloud",
  project: "dcpNLHMDvBQfgKbQ"
});

module.exports = async function() {
  const cache = flatcache.load("tags", path.resolve("./.data-cache"));
  const key = getCacheKey();
  const cachedData = cache.getKey(key);

  if (!cachedData) {
    console.log("Fetching tags");

    // Get all Tags
    const tags = await directus.getItems("tags").then(res => res.data);

    cache.setKey(key, tags);
    cache.save();
    return tags;
  }

  return cachedData;
}

function getCacheKey() {
  const date = new Date();
  return `${date.getUTCFullYear()}-${date.getUTCMonth() + 1}-${date.getUTCDate()} ${date.getHours()}:${date.getMinutes()}`;
}
