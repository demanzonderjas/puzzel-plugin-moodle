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

## Using it

Once the site is connected, a teacher adds Puzzel.org like any other activity:

1. In a course, turn on **Edit mode** and choose **Add an activity or
   resource**.
2. Pick **Puzzel.org** from the activity chooser.
3. Sign in to Puzzel.org if asked, then choose one of your activities.
4. Save. Students open the activity inside Moodle and play it there.

If the activity is graded, the score is written back to the Moodle gradebook
when a student finishes.

## Subscription

Placing and playing Puzzel activities is free. Sending scores back to the
Moodle gradebook requires a Puzzel.org subscription.

## Development

The plugin has no build step. To work on it:

- **Coding style** — `phpcs --standard=moodle-extra local/puzzel`, using
  [`moodlehq/moodle-cs`](https://github.com/moodlehq/moodle-cs). It is clean
  with zero warnings, and it should stay that way.
- **Tests** — `vendor/bin/phpunit --testsuite local_puzzel_testsuite` after
  `admin/tool/phpunit/cli/init.php`. The tests register a tool type exactly as
  Moodle's dynamic registration leaves one and assert what `tool::activate()`
  does to it, including that it does not drop the registered configuration.
- **CI** — `.github/workflows/ci.yml` runs moodle-plugin-ci against Moodle
  4.1, 4.5 and 5.2.

A full manual run needs a Moodle that Puzzel.org can reach: Puzzel refuses to
register a platform that is not on a public https address, so a local Moodle
needs a tunnel of its own plus `$CFG->wwwroot` and `$CFG->sslproxy`.

## Licence

GNU GPL v3 or later.
