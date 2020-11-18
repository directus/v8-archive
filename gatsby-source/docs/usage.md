# Usage

## Usage Examples

### Sample `gatsby-node.js`

```js
const path = require('path');

module.exports.createPages = async ({ graphql, actions }) => {
  const { createPage } = actions;

  // Sample query for all blog posts.
  const response = await graphql(`
    query GatsbyBlogPostsQuery {
      allDirectusBlogPost {
        edges {
          node {
            directusId
            title
            author
            body
            preview
            created
            slug
          }
        }
      }
    }
  `);

  // Destructure response to get post data
  const {
    data: {
      allDirectusBlogPost: { edges: posts = [] },
    },
  } = response;

  // Build a new page that could list the blog posts
  createPage({
    path: '/blog',
    component: path.resolve('./src/templates/blog-post-list.js'),
    context: posts,
  });

  // Build a new page for each blog post
  posts.forEach(({ node: post }) => {
    createPage({
      path: `/blog/${post.slug}`,
      component: path.resolve('./src/templates/blog-post.js'),
      context: post,
    });
  });
};
```

## Example Using Static Query

```js
// ./gatsby-node.js
const path = require('path');

module.exports.createPages = async ({ graphql, actions }) => {
  const { createPage } = actions;

  // Sample query for all blog posts.
  const response = await graphql(`
    query GatsbyNodeQuery {
      allDirectusBlogPost {
        edges {
          node {
            directusId
          }
        }
      }
    }
  `);

  // Destructure response to get post IDs
  const {
    data: {
      allDirectusBlogPost: { edges: posts = [] },
    },
  } = response;

  // Build a new page for each blog post, passing the directusId
  // via `context` for the static query
  posts.forEach(({ node: post }) => {
    createPage({
      path: `/blog/${post.slug}`,
      component: path.resolve('./src/templates/blog-post.js'),
      context: post,
    });
  });
};
```

```js
// ./src/templates/blog-post.js
import { graphql } from 'gatsby';
import React from 'react';

// A static query, the results from which
// will be passed to our component. Uses the 'directusId' property
// passed via the `createPage` context config to retrieve the blog post.
export const query = graphql`
  query($directusId: Int!) {
    directusBlogPost(directusId: {eq: $directusId}) {
      directusId
      title
      author
      body
      preview
      created
      slug
    }
  }
`;

// The component we'll render for a given blog post
const BlogPost = ({
  data: { directusBlogPost: post }
}) => {
  return (
    // ... Some markup consuming the 'post' data
  );

};

export default BlogPost;
```

## Sample Using a Page Query

```js
// ./src/pages/blog.js
import { graphql } from 'gatsby';
import React from 'react';

// The query used to provide the page data
// for the '/blog' route.
export const pageQuery = graphql`
    query BlogQuery {
      allDirectusBlogPost {
        edges {
          node {
            directusId
            title
            author
            body
            preview
            created
            slug
          }
        }
      }
    }
`;

// The component that will render a list of blog posts
const BlogPage = ({
    data,
    location
}) => {

  // Extracting post data from props.
  const {
      allDirectusBlogPost: posts = [],
  } = data;

  return (
    // ... Some markup consuming the list of 'posts'
  );

};

export default BlogPage;

```

## Query Examples

### Basic query for a list of blog posts

```graphql
query BlogQuery {
  allDirectusBlogPost {
    edges {
      node {
        directusId
        title
        author
        body
        preview
        created
        slug
      }
    }
  }
}
```

### Basic query for a single blog post (via directusId)

```graphql
query BlogPostQuery($directusId: Int!) {
  directusBlogPost(directusId: { eq: $directusId }) {
    directusId
    title
    author
    body
    preview
    created
    slug
  }
}
```

### Filtered & sorted list of blog posts

```graphql
query BlogPostQuery {
  allDirectusBlogPost(filter: { created: { gte: "2020-01-01 00:00:00" } }, sort: { order: DESC, fields: created }) {
    edges {
      node {
        directusId
        title
        author
        body
        preview
        created
        slug
      }
    }
  }
}
```

### Join for a list of products & all projects associated with each product.

Assumes a field `related_projects` exists as the join field on the `product` table in Directus.

```graphql
query ProductQuery {
  allDirectusProduct {
    edges {
      node {
        directusId
        name
        created
        related_projects {
          directusId
          name
        }
      }
    }
  }
}
```

### Join for the product category details via product ID.

Assumes a field `owning_product_category` exists as the join field on the `product` table in Directus.

```graphql
query ProductQuery($directusId: Int!) {
  directusProduct(directusId: { eq: $directusId }) {
    directusId
    name
    created
    owning_product_category {
      directusId
      name
      description
    }
  }
}
```

### Join for the images (stored as Directus files) associated with a product

Assumes a field `images` exists as the join field on the `product` table in Directus.

```graphql
query ProductQuery($directusId: Int!) {
  directusProduct(directusId: { eq: $directusId }) {
    directusId
    name
    created
    images {
      id
      data {
        full_url
        thumbnails {
          height
          width
          dimension
          url
        }
      }
      description
      type
      title
    }
  }
}
```

### Sample thumbnail query using `gatsby-image`

Builds a set of thumbnails (150px x 150px) for all images. Assumes `downloadFiles` was `true` in the plugin's config. The `originalName` property should match the corresponding `directusFile` `filename` property.

```graphql
query AllImageThumbnails {
  allImageSharp {
    edges {
      node {
        sizes(maxHeight: 150, maxWidth: 150) {
          src
          originalName
        }
      }
    }
  }
}
```
