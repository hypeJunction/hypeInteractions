<a name="5.2.0"></a>
# [5.2.0](https://github.com/hypeJunction/hypeInteractions/compare/5.1.2...v5.2.0) (2017-07-07)


### Features

* **ui:** integrate with hypeUI ([b76ffac](https://github.com/hypeJunction/hypeInteractions/commit/b76ffac))



<a name="5.1.2"></a>
## [5.1.2](https://github.com/hypeJunction/hypeInteractions/compare/5.0.0...v5.1.2) (2017-04-21)


### Bug Fixes

* **access:** make sure river object access is in sync with container ([be162bc](https://github.com/hypeJunction/hypeInteractions/commit/be162bc))
* **access:** river object access is now correctly set and respected in views ([88de0bc](https://github.com/hypeJunction/hypeInteractions/commit/88de0bc))
* **cs:** canComment declaration now matches that of ElggObject ([0715bf4](https://github.com/hypeJunction/hypeInteractions/commit/0715bf4)), closes [#8](https://github.com/hypeJunction/hypeInteractions/issues/8)
* **river:** river object page now displayed correctly ([e48d559](https://github.com/hypeJunction/hypeInteractions/commit/e48d559))

### Features

* **river:** actionable river objects work again ([6194dac](https://github.com/hypeJunction/hypeInteractions/commit/6194dac))



<a name="5.1.1"></a>
## [5.1.1](https://github.com/hypeJunction/hypeInteractions/compare/5.1.0...v5.1.1) (2017-04-19)


### Bug Fixes

* **cs:** canComment declaration now matches that of ElggObject ([0715bf4](https://github.com/hypeJunction/hypeInteractions/commit/0715bf4)), closes [#8](https://github.com/hypeJunction/hypeInteractions/issues/8)



<a name="5.1.0"></a>
# [5.1.0](https://github.com/hypeJunction/hypeInteractions/compare/5.0.0...v5.1.0) (2017-04-14)


### Features

* **river:** actionable river objects work again ([6194dac](https://github.com/hypeJunction/hypeInteractions/commit/6194dac))



<a name="5.0.0"></a>
# [5.0.0](https://github.com/hypeJunction/hypeInteractions/compare/4.2.3...v5.0.0) (2017-04-09)


### Bug Fixes

* **comment:** URL previews are rendered once again ([c73b7dc](https://github.com/hypeJunction/hypeInteractions/commit/c73b7dc))
* **ux:** popup menu now hides itself before loading the form ([bb94c4b](https://github.com/hypeJunction/hypeInteractions/commit/bb94c4b))
* **views:** bail if comment owner or entity can not be loaded ([35540b0](https://github.com/hypeJunction/hypeInteractions/commit/35540b0))

### Features

* **editor:** add a setting to use visual editor by default ([9cc66cd](https://github.com/hypeJunction/hypeInteractions/commit/9cc66cd))
* **elgg:** now requires Elgg 2.3 ([2121c4f](https://github.com/hypeJunction/hypeInteractions/commit/2121c4f))
* **settings:** update settings form to latest fields API ([61297a1](https://github.com/hypeJunction/hypeInteractions/commit/61297a1))


### BREAKING CHANGES

* elgg: The plugin now requires Elgg 2.3+



<a name="4.2.3"></a>
## [4.2.3](https://github.com/hypeJunction/hypeInteractions/compare/4.2.2...v4.2.3) (2016-12-05)


### Bug Fixes

* **notifications:** add missing param to notification body sprintf ([8c90445](https://github.com/hypeJunction/hypeInteractions/commit/8c90445)), closes [#6](https://github.com/hypeJunction/hypeInteractions/issues/6)



<a name="4.2.2"></a>
## [4.2.2](https://github.com/hypeJunction/hypeInteractions/compare/4.2.1...v4.2.2) (2016-10-03)


### Bug Fixes

* **order:** correct offset calculation ([3c5e538](https://github.com/hypeJunction/hypeInteractions/commit/3c5e538))



<a name="4.2.1"></a>
## [4.2.1](https://github.com/hypeJunction/hypeInteractions/compare/4.2.0...v4.2.1) (2016-10-03)


### Bug Fixes

* **js:** comment editing no longer presumes that menu is within the comment object view ([7d630c6](https://github.com/hypeJunction/hypeInteractions/commit/7d630c6))
* **ui:** comments are highlighted once again when their hash is present in the URL ([71c9cbc](https://github.com/hypeJunction/hypeInteractions/commit/71c9cbc))
* **ux:** comment posted date is now a permalink again ([8129c00](https://github.com/hypeJunction/hypeInteractions/commit/8129c00))
* **ux:** URL hash now takes to comments component ([90d3736](https://github.com/hypeJunction/hypeInteractions/commit/90d3736))



<a name="4.2.0"></a>
# [4.2.0](https://github.com/hypeJunction/hypeInteractions/compare/4.1.0...v4.2.0) (2016-09-21)


### Bug Fixes

* **css:** style elgg notice box ([632289b](https://github.com/hypeJunction/hypeInteractions/commit/632289b))
* **permissions:** fix can comment permissions for logged out users ([6c6668d](https://github.com/hypeJunction/hypeInteractions/commit/6c6668d))
* **river:** users can now reply to comments from the river ([2df315d](https://github.com/hypeJunction/hypeInteractions/commit/2df315d))

### Features

* **comments:** comments can now be searched and filtered within the comments block ([b80597a](https://github.com/hypeJunction/hypeInteractions/commit/b80597a))
* **core:** upgrade API to new coding standards ([477788a](https://github.com/hypeJunction/hypeInteractions/commit/477788a))



<a name="4.1.0"></a>
# [4.1.0](https://github.com/hypeJunction/hypeInteractions/compare/4.0.0...v4.1.0) (2016-09-15)


### Bug Fixes

* **js:** updates to client-side UX, API fixes ([e67e0b4](https://github.com/hypeJunction/hypeInteractions/commit/e67e0b4))
* **ui:** hide attachments form by default ([bf859c5](https://github.com/hypeJunction/hypeInteractions/commit/bf859c5))

### Features

* **css:** use FA icons, update some styles ([f143dde](https://github.com/hypeJunction/hypeInteractions/commit/f143dde))
* **input:** add a comment input better integrated with ckeditor ([99e1591](https://github.com/hypeJunction/hypeInteractions/commit/99e1591))
* **interactions:** simplify interactions menu and component styling ([1b60d15](https://github.com/hypeJunction/hypeInteractions/commit/1b60d15))



<a name="4.0.0"></a>
# [4.0.0](https://github.com/hypeJunction/hypeInteractions/compare/3.5.0...v4.0.0) (2016-09-13)


### Bug Fixes

* **actions:** fix typo in action ([9cd6131](https://github.com/hypeJunction/hypeInteractions/commit/9cd6131))
* **comments:** nested comment URL now point to first level comment in the tree ([f43034f](https://github.com/hypeJunction/hypeInteractions/commit/f43034f))
* **forms:** more consistency in submit button labels ([e223321](https://github.com/hypeJunction/hypeInteractions/commit/e223321))
* **forms:** more consistency in submit button labels ([45b1a16](https://github.com/hypeJunction/hypeInteractions/commit/45b1a16))
* **js:** fix form bugs ([5349ca0](https://github.com/hypeJunction/hypeInteractions/commit/5349ca0))
* **js:** focus form input when comment form is expanded ([fe89881](https://github.com/hypeJunction/hypeInteractions/commit/fe89881))
* **js:** less sensitive to menu item markup ([0bfdf13](https://github.com/hypeJunction/hypeInteractions/commit/0bfdf13))
* **likes:** do not display likes if likes plugin is disabled ([83e06e6](https://github.com/hypeJunction/hypeInteractions/commit/83e06e6))
* **lists:** fix reverse ordered lists ([97e2bfe](https://github.com/hypeJunction/hypeInteractions/commit/97e2bfe))
* **lists:** increase autorefresh time and disable lazyloading ([2ed10a3](https://github.com/hypeJunction/hypeInteractions/commit/2ed10a3))
* **notifications:** avoid sending duplicate notifications to content owners ([2768c75](https://github.com/hypeJunction/hypeInteractions/commit/2768c75))
* **notifications:** fix notifications strings ([e1bef73](https://github.com/hypeJunction/hypeInteractions/commit/e1bef73))
* **notifications:** fix reply notification string ([7d7c166](https://github.com/hypeJunction/hypeInteractions/commit/7d7c166))
* **notifications:** prevent duplicate notifications to content owner ([82bfeb9](https://github.com/hypeJunction/hypeInteractions/commit/82bfeb9))
* **notifications:** strip most tags from notifications ([63b5cfd](https://github.com/hypeJunction/hypeInteractions/commit/63b5cfd))
* **river:** correctly add new comments to river ([2f300f9](https://github.com/hypeJunction/hypeInteractions/commit/2f300f9))
* **threads:** threads now use comment type and subtype to build threads ([ea850b7](https://github.com/hypeJunction/hypeInteractions/commit/ea850b7))
* **ui:** correct when comments tab is expanded by default ([c645475](https://github.com/hypeJunction/hypeInteractions/commit/c645475))
* **ux:** do not expand second level comments in river ([13dcf48](https://github.com/hypeJunction/hypeInteractions/commit/13dcf48))
* **ux:** move comment edit and delete actions to the entity menu ([496ac62](https://github.com/hypeJunction/hypeInteractions/commit/496ac62))
* **views:** clean up views ([7a27d63](https://github.com/hypeJunction/hypeInteractions/commit/7a27d63))
* **views:** update comment body view ([57f8f33](https://github.com/hypeJunction/hypeInteractions/commit/57f8f33))
* **views:** update comment view to use object/elements/summary ([f661648](https://github.com/hypeJunction/hypeInteractions/commit/f661648))
* **views:** use actual owner icon ([2d37d7f](https://github.com/hypeJunction/hypeInteractions/commit/2d37d7f))



<a name="3.5.0"></a>
# [3.5.0](https://github.com/hypeJunction/hypeInteractions/compare/3.4.0...v3.5.0) (2016-03-20)


### Features

* **ux:** add a setting to expand comments tab by default ([bf2b65b](https://github.com/hypeJunction/hypeInteractions/commit/bf2b65b))



<a name="3.4.0"></a>
# [3.4.0](https://github.com/hypeJunction/hypeInteractions/compare/3.3.1...v3.4.0) (2016-01-24)


### Features

* **notifications:** improve notifications ([0a5ef64](https://github.com/hypeJunction/hypeInteractions/commit/0a5ef64))



<a name="3.3.1"></a>
## [3.3.1](https://github.com/hypeJunction/hypeInteractions/compare/3.3.0...v3.3.1) (2016-01-23)


### Bug Fixes

* **composer:** update dependencies to allow installs with hypeApps 5.x ([e6231ee](https://github.com/hypeJunction/hypeInteractions/commit/e6231ee))



<a name="3.3.0"></a>
# [3.3.0](https://github.com/hypeJunction/hypeInteractions/compare/3.1.3...v3.3.0) (2016-01-23)


### Bug Fixes

* **css:** use more specific selectors ([efa4fcf](https://github.com/hypeJunction/hypeInteractions/commit/efa4fcf))
* **js:** changes to core broke ajax requests ([7c908c2](https://github.com/hypeJunction/hypeInteractions/commit/7c908c2))
* **js:** remove bundled likes js and css ([03c7200](https://github.com/hypeJunction/hypeInteractions/commit/03c7200))
* **js:** trigger form initialize to instantiate dropzone ([5061ef3](https://github.com/hypeJunction/hypeInteractions/commit/5061ef3))

### Features

* **views:** standardized lists ([7b813aa](https://github.com/hypeJunction/hypeInteractions/commit/7b813aa))



