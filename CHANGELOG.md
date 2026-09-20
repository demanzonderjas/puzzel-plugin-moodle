# Changelog

All notable changes to this plugin are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the plugin uses
[semantic versioning](https://semver.org/).

## [0.1.0] - 2026-09-20

First release.

### Added

- A **Connect Puzzel.org** page under *Site administration → Plugins → Local
  plugins*, which runs Moodle's own LTI 1.3 dynamic registration against
  Puzzel.org and then finishes what it leaves undone: the new tool is
  activated and shown in the activity chooser, so teachers can find it
  without a site administrator visiting *Manage tools*.
- A **Finish setup** action for a registration that was completed elsewhere,
  or interrupted before the tool was activated.
- A notice before connecting when the site already has a Puzzel.org tool,
  because Moodle then offers to update that tool rather than adding a new one.
- A **Registration URL** setting, for pointing the plugin at a test site.
