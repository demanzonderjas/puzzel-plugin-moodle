# Puzzel.org for Moodle (`local_puzzel`)

Connects a Moodle site to [Puzzel.org](https://puzzel.org) as an LTI Advantage
(LTI 1.3) tool, and finishes the job Moodle's dynamic registration leaves
half done.

## What it does

Moodle can already register an LTI Advantage tool from a single URL. What it
cannot do is finish: `mod/lti/openid-registration.php` always creates the tool
`PENDING`, and `registration_to_config()` always sets its visibility to
`PRECONFIGURED`. Neither value can be set by the tool being registered. So
after a successful registration a site administrator still has to find the
tool under *Manage tools*, activate it, and switch it to *Show in activity
chooser* — and until they do, teachers see nothing.

This plugin runs the registration and then does exactly that, so the admin's
whole job is one button.

## Requirements

- Moodle 4.1 or later.
- A Puzzel.org account to confirm the connection with. The account that
  confirms becomes the owner of the connection.

## Installing

Copy this directory to `local/puzzel` in your Moodle installation (on Moodle
5.1 and later, `public/local/puzzel`), then visit *Site administration →
Notifications* to complete the installation.

Then go to *Site administration → Plugins → Local plugins → Puzzel.org →
Connect Puzzel.org* and press the button.

## Subscription

Placing and playing Puzzel activities is free. Sending scores back to the
Moodle gradebook requires a Puzzel.org subscription.

## Licence

GNU GPL v3 or later.
