const path = require("path");
const flatcache = require("flat-cache");
const sdk = require("@directus/sdk-js");

// Create a new instance of the Directus Javascript SDK
const directus = new sdk({
  url: "https://api.directus.cloud",
  project: "dcpNLHMDvBQfgKbQ"
});

module.exports = async function() {
  const cache = flatcache.load("articles", path.resolve("./.data-cache"));
  const key = getCacheKey();
  const cachedData = cache.getKey(key);

  // Get fresh data if there is non cached data
  if (true || !cachedData) {

    // Get all Articles from Directus Cloud API
    const articles = await directus.getItems("articles", {
      filter: {
        status: {
          eq: 'published'
        }
      },
      sort: "-publish_date",
      fields: ["*", "author.*.*", "hero.*", "tags.tag_id.name"]
    }).then(res => res.data);

    cache.setKey(key, articles);
    cache.save();
    return articles;
  }

  return cachedData;
}

function getCacheKey() {
  const date = new Date();
  return `${date.getUTCFullYear()}-${date.getUTCMonth() + 1}-${date.getUTCDate()} ${date.getHours()}:${date.getMinutes()}`;
}
