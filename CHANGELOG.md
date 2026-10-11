# Changelog

All notable changes to this plugin are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the plugin uses
[semantic versioning](https://semver.org/).

## [0.1.1] - 2026-10-11

Changes asked for in the Moodle plugin review.

### Changed

- Only a **pending** tool is ever activated. Returning from a connection
  activates the one tool that connection created and nothing else; a tool an
  administrator has already configured or rejected, or deliberately kept out of
  the activity chooser, is left exactly as it is. Before, every Puzzel.org
  LTI 1.3 tool on the site was activated and moved into the chooser.
- The page lists every Puzzel.org tool on the site with its name, client ID,
  registration date, state and visibility, and the activate button sits on the
  pending tool it applies to, so it is plain which tool it changes.
- `LICENSE` holds the full text of the GNU GPL v3 instead of a pointer to it.
- Source files carry the standard Moodle boilerplate header.

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
