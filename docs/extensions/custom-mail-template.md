# Custom Mail Template

You are able to create new templates files to replace current templates. The way this works is that you are able to create one or more of the 6 templates files into `public/extensions/custom/mail` and this file will have priority over the core template. These are Twig template files.

There are 6 files:

- `base.twig`
- `footer.twig`
- `forgot-password.twig`
- `reset-password.twig`
- `new-install.twig`
- `user-invitation.twig`

All the settings values in `directus_settings` and `api.project` are passed to each template file as data. Example, you can use `settings.project_name` to identify the project name.

## Base Template: `base.twig`

This file expects two blocks: `content` and `footer`.

## Footer Template: `footer.twig`

These is the default `footer` block of `base.twig`.

## Request New Password: `forgot-password.twig`

This email template is used to send the user a token to reset their password, when they request that they have forgotton their password.

By default this file extends `base.twig` and create the `block` content that will be used as main content.

The `reset_token` and `user_full_name` are passed to this template.

## Send New Password: `reset-password.twig`

This email template is used to send the user a new password, when they request that they have forgotton their password and confirm the change using the token sent in a previous email.

The `new_password` and `user_full_name` are passed to the template.

## New Installation: `new-install.twig`

This email template is used to send a welcome email to the default admin when creating a new project.

_Not Properly Implemented Yet_

## User Invitation: `user-invitation.twig`

This email template is used to send an invitation email to a new user.

The invitation `token` is passed to this template.

Adding one of these files into your custom directory (`public/extensions/custom/mail`) will be replaced with the core template giving you the opportunity to have your own custom email template.