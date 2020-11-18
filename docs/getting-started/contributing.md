# 👩‍💻 Contributing

> Our small team truly appreciates every contribution made by our community: feature requests, bug reports, and especially pull requests!. If you have _any_ questions please reach out to our Core team on [Slack](https://directus.chat).
se images are available through our [Docker Hub](https://hub.docker.com/u/directus/).

## Feature Requests

### 80/20 Rule

The main thing to be aware of when submitting a new Directus feature request, is our rule on edge-cases. To keep the Directus core codebase as clean and simple as possible we will only consider adding features that at least 80% of our user-base will use. If we feel that less than 80% of our users will find the feature valuable then we will not implement it. Instead, those edge-case features should be added as Extensions.

### Which Repository?

If your new feature is specific to the App (the part you see and use in the browser) then you'll want to submit [here](https://github.com/directus/app/issues/new?template=Feature_request.md). Otherwise, if it is an API feature you can submit [here](https://github.com/directus/api/issues/new?template=Feature_request.md). If you're not sure or the feature is more conceptual or global, then submit it to the App and we'll organize it for you!

### Browsing Existing Requests

Before adding a new request, you should also first [search](https://github.com/directus/app/issues?q=is%3Aissue+is%3Aopen+sort%3Areactions-%2B1-desc) to see if it has already been submitted. All feature requests should include the `enhancement` label, so you can filter by that. And remember to also check _closed_ issues since your feature might have already been submitted in the past and either [rejected](#Our-80/20-Rule) or already implemented.

Also, if you want to see the most highly requested features you can sort by `:+1:` (the thumbs-up emoji).

### Submitting a Request

If your idea passes the 80/20 test and has not already been submitted, then we'd love to hear it! Submit a new issue using the Feature Request template and be sure to include the `enhancement` label. It's important to completely fill our the template with as much useful information as possible so that we can properly review your request. If you have screenshots, designs, code samples, or any other helpful assets be sure to include those too!

### Voting on Requests

You can also vote on existing feature requests. As mentioned above, the `:+1:` and `:-1:` are used for sorting, so adding one of these reactions to the GitHub issue will cast a vote that helps us better identify the most desired (or undesired) features. And remember to add a comment if you have additional thoughts to help clarify or improve the request.

### Fulfilling a Request

Our core team is always working hard to implement the most highly-requested community features, but we're a small team. If you need the feature faster than we can provide it, or simply want to help improve the Directus platform, we'd love to receive a pull-request from you!


## Contributing code changes

### Simple Pull Requests

Before we get into the full-blown "proper" way to do a pull request, let's quickly cover an easier method you can use for _small_ fixes. This way is especially useful for fixing quick typos in the docs, but is not as safe for code changes since it bypasses validation and linting.

1. Sign in to GitHub
2. Go to the file you want to edit (eg: [this page](https://github.com/directus/directus-8-legacy/blob/master/getting-started/contributing.md))
3. Click the pencil icon to "Edit this file"
4. Make any changes
5. Describe and submit your changes within "Propose file change"

That's it! GitHub will create a fork of the project for you and submit the change to a new branch in that fork. Just remember to submit separate pull requests when solving different problems.

### Proper Pull Requests

_Loosely based on [this great Gist](https://gist.github.com/Chaser324/ce0505fbed06b947d962) by [Chaser324](https://gist.github.com/Chaser324)_

We like to keep a tight flow when working with GitHub to make sure we have a clear history and accountability of what changes were made and when. Working with Git, and especially the GitHub specific features like forking and creating pull requests, can be quite daunting for new users.

To help you out in your Git(Hub) adventures, we've put together the (fairly standard) flow of contributing to an open source repo.

#### Forking the repo

Whether you're working on the API or the App, you will need to have your own copy of the codebase to work on. Head to the repo of the project you want to help out with and hit the Fork button. This will create a full copy of the whole project for you on your own account.

To work on this copy, you can install the project locally according to the normal installation instructions, substituting the name `directus` with the name of your github account.

#### Keeping your fork up to date

If you're doing more work than just a tiny fix, it's a good idea to keep your fork up to date with the "live" or _upstream_ repo. This is the main Directus repo that contains the latest code. If you don't keep your fork up to date with the upstream one, you'll run into conflicts pretty fast. These conflicts will arise when you made a change in a file that changed in the upstream repo in the meantime.

##### On git remotes

When using git on the command line, you often pull and push to `origin`. You might have seen this term in certain commands, like

```bash
git push origin master
```

or

```bash
git pull origin new-feature
```

In this case, the word `origin` is refered to as a _remote_. It's basically nothing more than a name for the full git url you cloned the project from:

```bash
git push origin master
```

is equal to

```bash
git push git@github.com:username/repo.git master
```

A local git repo can have multiple remotes. While it's not very common to push your code to multiple repo's, it's very useful when working on open source projects. It allows you to add the upstream repo as another remote, making it possible to fetch the latest changes straight into your local project.

```bash
# Add 'upstream' to remotes
git remote add upstream git@github.com:directus/app.git
```

When you want to update your fork with the latest changes from the upstream project, you first have to fetch all the (new) branches and commits by running

```bash
git fetch upstream
```

When all the changes are fetched, you can checkout the branch you want to update and merge in the changes.

```
git checkout master
git rebase upstream/master
```

If you haven't made any commits on the branch you're updating, git will update your branch without complaints. If you _have_ created commits in the meantime, git will step by step apply all the commits from _upstream_ and try to add in the commit you made in the meantime. It is very plausible that conflicts arise at this stage. When you've changed something that also changed on the upstream, git requires you to resolve the conflict yourself before being able to move on.

::: danger Conflicts
You should always favor changes on upstream over your local ones.
:::

#### Doing Work

Whenever you begin working on a bugfix or new feature, make sure to create a new branch. This makes sure that your changes are organized and separated from the master branch, so you can submit and manage your pull requests for separate fixes/features more easily.

```bash
# Checkout the master branch - you want your new branch to come from master
git checkout master

# Create a new branch named newfeature (give your branch its own simple informative name)
git branch newfeature

# Switch to your new branch
git checkout newfeature
```

::: warning Up-to-date
Make sure to update your master branch with the one from upstream, so you're certain you start with the latest version of the project!
:::

#### Submitting a Pull Request

Prior to opening your pull request, you might want to update your branch a final time, so it can immediately be merged into the master branch of upstream.

```bash
# Fetch upstream master and merge with your repo's master branch
git fetch upstream
git checkout master
git merge upstream/master

# If there were any new commits, rebase your master branch
git checkout newfeature
git rebase master
```

::: warning
Make sure to check if your branch is up to date with the `master` branch of upstream. An outdated branch makes it near impossible for the maintainers of Directus to check and review the pull request and will most likely result in a delayed merge.
:::

Once you've commited and pushed all the changes on your branch to your fork on GitHub, head over to GitHub, select your branch and hit the pull request button.

You can still push new commits to a pull request that already has been opened. This way, you can fix certain comments reviewers might have left.

::: tip
Please allow the maintainers of upstream to push commits to your fork by leaving the "Allow edits from maintainers" option turned on. This allows our Core Team to help out in your PR!
:::
