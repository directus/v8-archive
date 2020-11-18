---
pageClass: page-reference
---

# Files

<two-up>

::: slot left
Files can be saved in any given location. Directus has a powerful assets endpoint that can be used to generate thumbnails for images on the fly.
:::

<info-box title="Endpoints" slot="right">

```endpoints
   GET /:project/files
   GET /:project/files/:id
  POST /:project/files
 PATCH /:project/files/:id
DELETE /:project/files/:id
   GET /:project/files/:id/revisions
   GET /:project/files/:id/revisions/:offset
```

</info-box>
</two-up>

---

## The File Object

### Attributes

<two-up>
<template slot="left">
<def-list>

#### id <def-type>integer</def-type>
Unique identifier for the file.

#### storage <def-type>string</def-type>
Where the file is stored. Either `local` for the local filesystem or the name of the storage adapter (for example `s3`).

#### filename_disk <def-type>string</def-type>
Name of the file on disk. By default, Directus uses a random hash for the filename.

#### filename_download <def-type>string</def-type>
How you want to the file to be named when it's being downloaded.

#### title <def-type>string</def-type>
Title for the file. Is extracted from the filename on upload, but can be edited by the user.

#### type <def-type>string</def-type>
MIME type of the file.

#### uploaded_by <def-type>user</def-type>
Who uploaded the file.

#### uploaded_on <def-type>datetime</def-type>
When the file was uploaded.

#### charset <def-type>string</def-type>
Character set of the file.

#### filesize <def-type>integer</def-type>
Size of the file in bytes.

#### width <def-type>integer</def-type>
Width of the file in pixels. Only applies to images.

#### height <def-type>integer</def-type>
Height of the file in pixels. Only applies to images.

#### duration <def-type>integer</def-type>
Duration of the file in seconds. Only applies to audio and video.

#### embed <def-type>string</def-type>
Where the file was embedded from.

#### folder <def-type>folder object</def-type>
Virtual folder where this file resides in.

#### description <def-type>string</def-type>
Description for the file.

#### location <def-type>string</def-type>
Where the file was created. Is automatically populated based on EXIF data for images.

#### tags <def-type>csv, string</def-type>
Tags for the file. Is automatically populated based on EXIF data for images.

#### checksum <def-type>string</def-type>
Represents the sum of the correct digits of the file, can be used to detect errors in and duplicates of the file later.

#### private_hash <def-type>string</def-type>
Random hash used to access the file privately. This can be rotated to prevent unauthorized access to the file.

#### metadata <def-type>key/value</def-type>
User provided miscellaneous key value pairs that serve as additional metadata for the file.

#### data.full_url <def-type>string</def-type>
Full URL to the original file.

#### data.url <def-type>string</def-type>
Relative URL to the original file.

#### data.thumbnails <def-type>array</def-type>
List of all available asset sizes with links.

#### data.thumbnails.url <def-type>string</def-type>
Full URL to the thumbnail.

#### data.thumbnails.relative_url <def-type>string</def-type>
Relative URL to the thumbnail.

#### data.thumbnails.dimension <def-type>string</def-type>
Width x height of the thumbnail.

#### data.thumbnails.width <def-type>integer</def-type>
Width of the thumbnail in pixels.

#### data.thumbnails.height <def-type>integer</def-type>
Height of the thumbnail in pixels.

</def-list>
</template>

<info-box title="File Object" slot="right" class="sticky">

```json
{
  "id": 3,
  "storage": "local",
  "filename_disk": "a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
  "filename_download": "avatar.jpg",
  "title": "User Avatar",
  "type": "image/jpeg",
  "uploaded_by": 1,
  "uploaded_on": "2019-12-03T00:10:15+00:00",
  "charset": "binary",
  "filesize": 137862,
  "width": 800,
  "height": 838,
  "duration": 0,
  "embed": null,
  "folder": null,
  "description": "",
  "location": "",
  "tags": [],
  "checksum": "d41d8cd98f00b204e9800998ecf8427e",
  "private_hash": "pnw7s9lqy68048g0",
  "metadata": null,
  "data": {
    "full_url": "https://demo.directus.io/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
    "url": "/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
    "thumbnails": [
      {
        "url": "https://demo.directus.io/thumper/assets/pnw7s9lqy68048g0?key=directus-small-crop",
        "relative_url": "/thumper/assets/pnw7s9lqy68048g0?key=directus-small-crop",
        "dimension": "64x64",
        "width": 64,
        "height": 64
      },
      { ... },
      { ... }
    ],
    "embed": null
  }
}
```

</info-box>
</two-up>

---

## List the Files

<two-up>
<template slot="left">

List the files.

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/limit.md !!!
!!! include query/offset.md !!!
!!! include query/sort.md !!!
!!! include query/single.md !!!
!!! include query/status.md !!!
!!! include query/filter.md !!!
!!! include query/q.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns an array of [file objects](#the-file-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/files
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 3,
      "storage": "local",
      "filename_disk": "a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
      "filename_download": "avatar.jpg",
      "title": "User Avatar",
      "type": "image/jpeg",
      "uploaded_by": 1,
      "uploaded_on": "2019-12-03T00:10:15+00:00",
      "charset": "binary",
      "filesize": 137862,
      "width": 800,
      "height": 838,
      "duration": 0,
      "embed": null,
      "folder": null,
      "description": "",
      "location": "",
      "tags": [],
      "checksum": "d41d8cd98f00b204e9800998ecf8427e",
      "private_hash": "pnw7s9lqy68048g0",
      "metadata": null,
      "data": {
        "full_url": "https://demo.directus.io/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
        "url": "/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
        "thumbnails": [
          {
            "url": "https://demo.directus.io/thumper/assets/pnw7s9lqy68048g0?key=directus-small-crop",
            "relative_url": "/thumper/assets/pnw7s9lqy68048g0?key=directus-small-crop",
            "dimension": "64x64",
            "width": 64,
            "height": 64
          },
          { ... },
          { ... }
        ],
        "embed": null
      }
    },
    { ... },
    { ... }
  ]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve a File

<two-up>
<template slot="left">

Retrieve a single file by unique identifier.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [file object](#the-file-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/files/:id
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 3,
    "storage": "local",
    "filename_disk": "a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
    "filename_download": "avatar.jpg",
    "title": "User Avatar",
    "type": "image/jpeg",
    "uploaded_by": 1,
    "uploaded_on": "2019-12-03T00:10:15+00:00",
    "charset": "binary",
    "filesize": 137862,
    "width": 800,
    "height": 838,
    "duration": 0,
    "embed": null,
    "folder": null,
    "description": "",
    "location": "",
    "tags": [],
    "checksum": "d41d8cd98f00b204e9800998ecf8427e",
    "private_hash": "pnw7s9lqy68048g0",
    "metadata": null,
    "data": {
      "full_url": "https://demo.directus.io/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
      "url": "/uploads/thumper/originals/a88c3b72-ac58-5436-a4ec-b2858531333a.jpg",
      "thumbnails": [
        {
          "url": "https://demo.directus.io/thumper/assets/pnw7s9lqy68048g0?key=directus-small-crop",
          "relative_url": "/thumper/assets/pnw7s9lqy68048g0?key=directus-small-crop",
          "dimension": "64x64",
          "width": 64,
          "height": 64
        }
      ],
      "embed": null
    }
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Create a File

<two-up>
<template slot="left">

Create a new file.

::: tip
It's recommend to use `multipart/form-data` as encoding type for this request.
:::

### Parameters

<def-list>

!!! include params/project.md !!!

</def-list>

### Attributes

<def-list>

#### data <def-type alert>required</def-type>
Raw file data (`multipart/form-data`), base64 string of file data, or URL you want to embed.

#### filename_download <def-type>optional</def-type>
How you want to the file to be named when it's being downloaded.

#### title <def-type>optional</def-type>
Title for the file. Is extracted from the filename on upload, but can be edited by the user.

#### folder <def-type>optional object</def-type>
Virtual folder where this file resides in.

#### description <def-type>optional</def-type>
Description for the file.

#### location <def-type>optional</def-type>
Where the file was created. Is automatically populated based on EXIF data for images.

#### tags <def-type>optional, string</def-type>
Tags for the file. Is automatically populated based on EXIF data for images.

#### metadata <def-type>optional/value</def-type>
User provided miscellaneous key value pairs that serve as additional metadata for the file.

</def-list>

### Query

<def-list>

!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [file object](#the-file-object) for the file that was just uploaded.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
  POST /:project/files
```

</info-box>

<info-box title="Request">

```json
{
  "data": "https://images.unsplash.com/photo-1576854531280-9087cfd26e86"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 50,
    "storage": "local",
    "filename_disk": "904695e3-bd5b-4ba5-a569-6f481f08a285.jpeg",
    "filename_download": "photo-1576854531280-9087cfd26e86.jpeg",
    "title": "Photo 1576854531280 9087cfd26e86",
    "type": "image/jpeg",
    "uploaded_by": 1,
    "uploaded_on": "2020-01-14T17:14:22+00:00",
    "charset": "binary",
    "filesize": 17585956,
    "width": 6000,
    "height": 4000,
    "duration": 0,
    "embed": null,
    "folder": null,
    "description": "",
    "location": "",
    "tags": [
      "photo  by dylan nolte"
    ],
    "checksum": "9d58a1f44b9bcf9faca50ff240ff3a36",
    "private_hash": "2aoxvcqi1jvooo8c",
    "metadata": null,
    "data": {
      "full_url": "https://demo.directus.io/uploads/thumper/originals/904695e3-bd5b-4ba5-a569-6f481f08a285.jpeg",
      "url": "/uploads/thumper/originals/904695e3-bd5b-4ba5-a569-6f481f08a285.jpeg",
      "thumbnails": [
        {
          "url": "https://demo.directus.io/thumper/assets/2aoxvcqi1jvooo8c?key=directus-small-crop",
          "relative_url": "/thumper/assets/2aoxvcqi1jvooo8c?key=directus-small-crop",
          "dimension": "64x64",
          "width": 64,
          "height": 64
        },
        { ... },
        { ... }
      ],
      "embed": null
    }
  }
}
```

</info-box>
</div>
</template>
</two-up>

---

## Update a File

<two-up>
<template slot="left">

Update an existing file

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Attributes

<def-list>

#### data <def-type alert>required</def-type>
Raw file data (`multipart/form-data`), base64 string of file data, or URL you want to embed.

#### filename_disk <def-type>optional</def-type>
Name of the file on disk.

#### filename_download <def-type>optional</def-type>
How you want to the file to be named when it's being downloaded.

#### title <def-type>optional</def-type>
Title for the file. Is extracted from the filename on upload, but can be edited by the user.

#### folder <def-type>optional object</def-type>
Virtual folder where this file resides in.

#### description <def-type>optional</def-type>
Description for the file.

#### location <def-type>optional</def-type>
Where the file was created. Is automatically populated based on EXIF data for images.

#### tags <def-type>optional, string</def-type>
Tags for the file. Is automatically populated based on EXIF data for images.

#### metadata <def-type>optional/value</def-type>
User provided miscellaneous key value pairs that serve as additional metadata for the file.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [file object](#the-file-object) for the file that was just updated.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
 PATCH /:project/files/:id
```

</info-box>

<info-box title="Request">

```json
{
  "title": "Dylan's Photo"
}
```

</info-box>

<info-box title="Response">

```json
{
  "data": [{
    "id": 50,
    "storage": "local",
    "filename_disk": "904695e3-bd5b-4ba5-a569-6f481f08a285.jpeg",
    "filename_download": "photo-1576854531280-9087cfd26e86.jpeg",
    "title": "Dylan's Photo",
    "type": "image/jpeg",
    "uploaded_by": 1,
    "uploaded_on": "2020-01-14T17:14:22+00:00",
    "charset": "binary",
    "filesize": 17585956,
    "width": 6000,
    "height": 4000,
    "duration": 0,
    "embed": null,
    "folder": null,
    "description": "",
    "location": "",
    "tags": [
      "photo  by dylan nolte"
    ],
    "checksum": "9d58a1f44b9bcf9faca50ff240ff3a36",
    "private_hash": "2aoxvcqi1jvooo8c",
    "metadata": null,
    "data": {
      "full_url": "https://demo.directus.io/uploads/thumper/originals/904695e3-bd5b-4ba5-a569-6f481f08a285.jpeg",
      "url": "/uploads/thumper/originals/904695e3-bd5b-4ba5-a569-6f481f08a285.jpeg",
      "thumbnails": [
        {
          "url": "https://demo.directus.io/thumper/assets/2aoxvcqi1jvooo8c?key=directus-small-crop",
          "relative_url": "/thumper/assets/2aoxvcqi1jvooo8c?key=directus-small-crop",
          "dimension": "64x64",
          "width": 64,
          "height": 64
        },
        { ... },
        { ... }
      ],
      "embed": null
    }
  }]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Delete a File

<two-up>
<template slot="left">

Delete an existing file

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Returns

Returns an empty body with HTTP status 204

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
DELETE /:project/files/:id
```

</info-box>
</div>
</template>
</two-up>

---

## List File Revisions

<two-up>
<template slot="left">

List the revisions made to the given file.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/id.md !!!

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/limit.md !!!
!!! include query/offset.md !!!
!!! include query/page.md !!!
!!! include query/sort.md !!!
!!! include query/single.md !!!
!!! include query/filter.md !!!
!!! include query/q.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns an array of [revision objects](/api/revisions.html#the-revision-object).

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/files/:id/revisions
```

</info-box>
<info-box title="Response">

```json
{
  "data": [
    {
      "id": 54,
      "activity": 67,
      "collection": "directus_files",
      "item": "1",
      "data": {
        "id": "1",
        "storage": "local",
        "filename": "28596128-90a0-5872-ba5e-ecb063407146.jpg",
        "title": "Green Fern Plants 1028223",
        "type": "image/jpeg",
        "uploaded_by": "1",
        "uploaded_on": "2019-11-06 20:30:17",
        "charset": "binary",
        "filesize": "3017750",
        "width": "5184",
        "height": "3456",
        "duration": "0",
        "embed": null,
        "folder": null,
        "description": "",
        "location": "",
        "tags": [],
        "checksum": "8d8bd7c4d1fae9e4d6e3b08c54f2a5df",
        "metadata": null,
        "data": {
          "full_url": "http://localhost:8080/uploads/_/originals/28596128-90a0-5872-ba5e-ecb063407146.jpg",
          "url": "/uploads/_/originals/28596128-90a0-5872-ba5e-ecb063407146.jpg",
          "thumbnails": [
            {
              "url": "http://localhost:8080/thumbnail/_/200/200/crop/good/28596128-90a0-5872-ba5e-ecb063407146.jpg",
              "relative_url": "/thumbnail/_/200/200/crop/good/28596128-90a0-5872-ba5e-ecb063407146.jpg",
              "dimension": "200x200",
              "width": 200,
              "height": 200
            }
          ],
          "embed": null
        }
      },
      "delta": {
        "filename": "green-fern-plants-1028223.jpg",
        "uploaded_by": 1,
        "uploaded_on": "2019-11-06 15:30:17"
      },
      "parent_collection": null,
      "parent_item": null,
      "parent_changed": false
    },
    { ... },
    { ... }
  ]
}
```

</info-box>
</div>
</template>
</two-up>

---

## Retrieve a File Revision

<two-up>
<template slot="left">

Retrieve a single revision of the file by offset.

### Parameters

<def-list>

!!! include params/project.md !!!
!!! include params/collection.md !!!

#### offset <def-type alert>required</def-type>
How many revisions to go back in time.

</def-list>

### Query

<def-list>

!!! include query/fields.md !!!
!!! include query/meta.md !!!

</def-list>

### Returns

Returns the [revision object](/api/revisions.html#the-revision-object) for the given unique identifier.

</template>

<template slot="right">
<div class="sticky">
<info-box title="Endpoint">

```endpoints
   GET /:project/files/:id/revisions/:offset
```

</info-box>

<info-box title="Response">

```json
{
  "data": {
    "id": 54,
    "activity": 67,
    "collection": "directus_files",
    "item": "1",
    "data": {
      "id": "1",
      "storage": "local",
      "filename": "28596128-90a0-5872-ba5e-ecb063407146.jpg",
      "title": "Green Fern Plants 1028223",
      "type": "image\/jpeg",
      "uploaded_by": "1",
      "uploaded_on": "2019-11-06 20:30:17",
      "charset": "binary",
      "filesize": "3017750",
      "width": "5184",
      "height": "3456",
      "duration": "0",
      "embed": null,
      "folder": null,
      "description": "",
      "location": "",
      "tags": [],
      "checksum": "8d8bd7c4d1fae9e4d6e3b08c54f2a5df",
      "metadata": null,
      "data": {
        "full_url": "http:\/\/localhost:8080\/uploads\/_\/originals\/28596128-90a0-5872-ba5e-ecb063407146.jpg",
        "url": "\/uploads\/_\/originals\/28596128-90a0-5872-ba5e-ecb063407146.jpg",
        "thumbnails": [
          {
            "url": "http:\/\/localhost:8080\/thumbnail\/_\/200\/200\/crop\/good\/28596128-90a0-5872-ba5e-ecb063407146.jpg",
            "relative_url": "\/thumbnail\/_\/200\/200\/crop\/good\/28596128-90a0-5872-ba5e-ecb063407146.jpg",
            "dimension": "200x200",
            "width": 200,
            "height": 200
          }
        ],
        "embed": null
      }
    },
    "delta": {
      "filename": "green-fern-plants-1028223.jpg",
      "uploaded_by": 1,
      "uploaded_on": "2019-11-06 15:30:17"
    },
    "parent_collection": null,
    "parent_item": null,
    "parent_changed": false
  }
}
```

</info-box>
</div>
</template>
</two-up>
