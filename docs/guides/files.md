# Files & Thumbnails

> Directus offers a full Digital Asset Management (DAM) system, multiple storage adapters, private file permissions, and on-demand thumbnail generation.

## Original Files

The location of your actual file originals is based on the project configuration. The example below shows how things might look when using the installation defaults (local filesystem storage adapter, default path, and UUID file naming):

```
example.com/uploads/<project-name>/originals/<filename_disk>
example.com/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg
```

![Original File](../img/asset-generator/original.jpg)
_Original File Used Below — 602KB and 1800x1200_

## Asset Middleware

You can also access an alias of files via our API's asset middleware. Using this URL allows you to control file permissions, rotate the private access hash, rename files on download, and generate dynamic thumbnails.

```
example.com/<project-name>/assets/<private_hash>
example.com/thumper/assets/pnw7s9lqy68048g0
```

::: tip
When a user downloads a file from the file middleware, its file name is dynamically updated to the value of `directus_files.filename_download` (set to the original file name).
:::

## Requesting Thumbnails

Fetching thumbnails is as easy as adding query parameters to the original file's middleware URL. If a requested thumbnail doesn't yet exist, it is dynamically generated and immediately returned.

```
example.com/<project-name>/assets/<private_hash>?<query>
example.com/thumper/assets/pnw7s9lqy68048g0?w=200&h=200&f=crop&q=80
example.com/thumper/assets/pnw7s9lqy68048g0?key=card
```

::: tip
The last example above uses the whitelist key as a shortcut for referencing a specific thumbnail option. For example: `example.com/<project-name>/assets/<private_hash>?key=<key>`
:::

When new assets are generated, they are stored in a `generated` directory alongside the original files. You should _not_ reference files via these URLs though, as these links do not dynamically generate thumbnails.

```
example.com/uploads/<project-name>/generated/<query>/<id>
example.com/uploads/thumper/generated/h200,w200,fcrop,q80/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg
```

## Whitelisting Thumbnails

The thumbnail whitelist is stored within the `directus_settings` collection. Each whitelist row consists of five fields:

* **Key**: A unique name to to make accessing thumbnails easier
* **Width**: The width of the image in pixels
* **Height**: The height of the image in pixels
* **Quality**: The image compression (`0-100`)
* **Fit**: Supports `crop` (exact requested width/height) and `contain` (maintains aspect ratio)

::: warning
Removing all options from the Thumbnail Whitelist will allow *any* images to be dynamically generated. This might allow malicious users to flood your storage with unwanted generated thumbnails.
:::

### Crop vs Contain

For easier comparison, both of the examples below were requested at `200` width, `200` height, and `75` quality. The `crop` thumbnail forces the dimensions, trimming the outside edges if needed. The `contain` thumbnail always maintains its aspect ratio, shrinking the image to fit _within_ the dimensions.

| Crop | Contain |
|------|------|
| ![Crop](../img/asset-generator/200-200-crop-75.jpg)<br>_8KB • 200x200_ | ![Contain](../img/asset-generator/200-200-contain-75.jpg)<br>_6KB • 200x133_ |

:::tip
Images are never stretched or distorted even when changing the aspect ratio.
:::

### Quality vs Filesize

The quality parameter can be any integer from `0-100`. Qualities closer to `0` have more compression artifacts therefore poor image quality, but lower filesizes. Values closer to `100` have less compression and better image quality, but larger filesizes. Below are four possible qualities (200x200 crop) to visually compare the balance between compression and filesize.

| 25% | 50% | 75% | 100% |
|------|------|--------|------|
| ![25%](../img/asset-generator/200-200-crop-25.jpg)<br>_4KB_ | ![50%](../img/asset-generator/200-200-crop-50.jpg)<br>_6KB_ | ![75%](../img/asset-generator/200-200-crop-75.jpg)<br>_8KB_ | ![100%](../img/asset-generator/200-200-crop-100.jpg)<br>_38KB_ |

## File Settings

Files are saved based on your project's configuration, with the following options:

* **File Naming** — The naming convention for the original/master file uploads.
  * **UUID** — (Default) A unique hash for greater privacy.
  * **Original Name** — The original name provided during upload.
* **File MimeType Whitelist** — A CSV of mimetypes allowed for upload, eg; `image/jpeg`
* **Asset Whitelist** — A listing of allowed thumbnail combinations, helps avoid malicious users from generating too many unneeded thumbnails on your server. See how to configure this [below](#generated-asset-whitelist).
* **YouTube API Key** — A developer API key that allows more detailed information to be fetched from YouTube embeds added to the Directus File Library.

## File Fields

Below are a few Directus Files fields, and how they are used by our Asset Middleware.

* **Title** — A title used for referencing the file within the App
* **Filename Disk** — The name of the original file on the storage disk. Changing this breaks any hardcoded references to the original file URL, but not relational links or URL references through the middleware.
* **Filename Download** — How files are renamed (content disposition) when downloaded through the asset middleware. Directus stores the original file name here by default.
* **Private Hash** — A randomly generated hash used to access the file within the middleware URL. This can be changed to remove existing file access, without changing the original file name/path.
* **Checksum** — Used to confirm the file integrity after downloading it
* **Storage** — The storage adapter used. Currently only one storage adapter can be set per project.
* **MetaData** — Can be used to store any additional key-value pairs of metadata
* **Tags** — Tags used to help find files when searching.
* **Embed** — This stores the external embed id when linking with services like YouTube or Vimeo.
* **Filesize** — The filesize in bytes. Does not apply to externally embedded files.
* **Duration** — The duration in seconds. Only applies to video files.
* **Type** — The mimetype of the file, for example: `image/jpeg`
* **Uploaded By** — The Directus User that uploaded the file

::: tip
Directus scrapes the file's IPTC and metadata upon upload. This will automatically fill-in values for: title, location, tags (keywords), and description.
:::
