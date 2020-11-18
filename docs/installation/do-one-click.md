# Using the DigitalOcean One-Click

Our friends over at [DigitalOcean](https://digitalocean.com) provide an easy-to-use one-click for automatically configuring new Droplets with Directus pre-installed.

[[toc]]

## Step 1: Register for DigitalOcean (if you haven't already)

If you don't have an account yet, create one now. If you use [our referral link](https://m.do.co/c/389c62a20bb5), you will get $100 in DigitalOcean credit! 

## Step 2: Create a new Droplet through the Marketplace

Go to [marketplace.digitalocean.com/apps/directus-1](https://marketplace.digitalocean.com/apps/directus-1) and hit the big blue "Create Directus Droplet" button in the top right.

## Step 3: Choose a plan & create the Droplet

Directus is able to run on any of the plans that DigitalOcean offers. The $10/mo plan offers a nice balance of cost vs power and should be sufficient for most website uses. If you're expecting a high throughput on the API, we recommend upping the CPU and RAM as needed.

::: warning Enabling HTTPS
The DigitalOcean One-Click sets you up with an installation of Apache, which means you can use Certbot to automatically configure an SSL certificate. Follow this tutorial to set this up: [How To Secure Apache with Let's Encrypt on Ubuntu 18.04](https://www.digitalocean.com/community/tutorials/how-to-secure-apache-with-let-s-encrypt-on-ubuntu-18-04)

You're required to run Directus using HTTPS.
:::

## Step 4: Login to your newly created Droplet

When you login for the first time, the Droplet will display some important information about your installation, including database username and password. Make note of these values, as you need them in the next step.

## Step 5: Install your first project

Open your new Directus installation in the browser. It will take you straight to the installation wizard for your first project. Use the credentials you were given in the previous step to continue.

::: tip Updating
The one-click sets you up with a Git based installation. See [the git instructions](/installation/git) for more information on how to update to the latest version of Directus.
:::