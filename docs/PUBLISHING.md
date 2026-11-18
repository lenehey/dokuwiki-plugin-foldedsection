# Publishing Folded Section

The repository's code and its entry in DokuWiki's plugin directory are separate. There are two native administration-interface installation routes:

- **Manual Install → URL/upload:** needs an installable ZIP; a URL must work without authentication.
- **Search and Install:** additionally needs a published, indexed page at `plugin:foldedsection` on dokuwiki.org.

## One-time publication

1. Make [the GitHub repository](https://github.com/lenehey/dokuwiki-plugin-foldedsection/settings) public. Confirm the repository and ZIP can be opened without a GitHub login.
2. Confirm the [latest test run](https://github.com/lenehey/dokuwiki-plugin-foldedsection/actions) passes. It uses the native Extension Manager archive installer, followed by the parser regression tests.
3. On a disposable DokuWiki, use **Admin → Extension Manager → Manual Install** with:
   
   https://github.com/lenehey/dokuwiki-plugin-foldedsection/archive/refs/heads/main.zip

4. Create or edit [plugin:foldedsection](https://www.dokuwiki.org/plugin:foldedsection) using your dokuwiki.org account. Check that the name is available before creating it. Copy the complete content of [dokuwiki-plugin-page.txt](dokuwiki-plugin-page.txt) into the page.
5. Check the rendered plugin information panel: description, author, type, update date, tested compatibility, download, source, and issue tracker. Do not declare Wrap or folded as dependencies: both are optional.
6. After directory indexing/cache refresh, search for `foldedsection` in a DokuWiki's **Search and Install** tab and install it. Verify that the installed directory is `lib/plugins/foldedsection/`.

Until those external publication checks are complete, do not describe the plugin as publicly listed or searchable.

## Maintainer identity and support

The author is Leonard Heyman and the public support channel is GitHub Issues. The email field is intentionally empty; add a maintainer-approved public address if desired. No private profile email is published automatically.

## Release updates

- Use a new ISO date (`YYYY-MM-DD`) in `plugin.info.txt` and the directory page's `lastupdate` field. Extension Manager compares these dates, rather than the changelog's semantic version.
- Update the changelog and relevant documentation.
- Run CI, including archive install/update and parser checks.
- Keep the directory's tested compatibility accurate; currently only DokuWiki 2026-07-14 “Mort” (with maintenance releases) is claimed.
- The directory download URL tracks `main.zip`. Only merge tested installable code into main. An immutable release ZIP may replace this URL later; update the directory metadata when changing it.

## Package conventions

- `plugin.info.txt` declares `base foldedsection`, author, release date, name, description, project URL, and `phpmin 8.2`.
- The installer discovers `plugin.info.txt` recursively and uses its base name, so GitHub's archive wrapper folder does not need to be renamed by administrators.
- `.gitattributes` excludes development, CI, and directory-publication files from git/GitHub archives.
- Runtime PHP, JavaScript, CSS, documentation, and GPL-2.0-only license remain in the package.
- CI's `foldedsection` artifact contains `foldedsection.zip`. Extract the artifact wrapper ZIP before uploading the inner installable ZIP through Extension Manager.

## Research sources

Reviewed on 2026-11-18:

- [DokuWiki's native Installer](https://github.com/dokuwiki/dokuwiki/blob/stable/lib/plugins/extension/Installer.php): URL/upload installation, archive extraction, plugin-info discovery, PHP compatibility, install/update, and cache purging.
- [DokuWiki's Extension metadata](https://github.com/dokuwiki/dokuwiki/blob/stable/lib/plugins/extension/Extension.php): base name, release-date update detection, `phpmin`, and dependencies.
- [Extension Manager repository client](https://github.com/dokuwiki/dokuwiki/blob/stable/lib/plugins/extension/Repository.php): searches the central dokuwiki.org repository API.
- [Official plugin-directory entry parser](https://github.com/dokuwiki/dokuwiki-plugin-pluginrepo/blob/master/syntax/entry.php): `---- plugin ----` metadata, first-level `plugin:` pages, download URL, tested compatibility, author, tags, and source/bug tracker.
- [Official example directory entry](https://github.com/dokuwiki/dokuwiki-plugin-pluginrepo/blob/master/setup/test/plugin/actionlink.txt).
- [Published Wrap plugin metadata](https://github.com/selfthinker/dokuwiki_plugin_wrap/blob/master/plugin.info.txt): standard plugin-info conventions.

The documentation pages at dokuwiki.org were unavailable to this session's web fetcher; the requirements above were checked against the official implementation and maintained plugin examples instead.
