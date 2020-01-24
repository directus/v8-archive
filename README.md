<h1>
  <img src="https://user-images.githubusercontent.com/522079/43096167-3a1b1118-8e86-11e8-9fb2-7b4e3b1368bc.png" width="40" alt="Directus Logo"/>&nbsp;&nbsp;Directus API
</h1>

<div align="center">
    <h2>Laravel branch status</h2>
    <a href="">
        <img src="https://github.com/directus/api/workflows/Build/badge.svg?branch=laravel" />
    </a>
    <a href="https://coveralls.io/github/directus/api?branch=laravel">
        <img src="https://coveralls.io/repos/github/directus/api/badge.svg?branch=laravel" />
    </a>
</div>

---

# WIP

This is a work-in-progress branch. Please **DO NOT** use this for any kind of environment besides development, testing and out of curiosity only.

# Objectives

-   Properly tested codebase
    -   Keep track of code coverage
    -   Static code analysis
-   Code quality
    -   Linting
    -   Code style checks and fixes

# Installing

To install Directus laravel package, for now you should add the git repository to your project's `composer.json` file. Then you can use the `directus/directus` package using `dev-laravel` version.

## Add the repository

```json
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:directus/api.git"
        }
    ]
```

## Add the dependency

```
composer require directus/directus:dev-laravel
```
