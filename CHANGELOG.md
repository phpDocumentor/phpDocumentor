2021-08-20: Version 3.1.1

# Fixed
- Add workaround for phar distribution and plantuml, thanks to [Jaapio]
- XML template invalid XML due to missing marker.type, #2986, thanks to [Jaapio]
- Class's traits not showing up in docs, #2984, thanks to [Jaapio]
- Files with forced global namespace fail with "" is not a valid Fqsen, #2967, thanks to [Jaapio]
- XML output missing method tags, #2965, thanks to [Jaapio]

# CI
- Updated code style checks, thanks to [jrfnl]


2021-07-07: Version 3.1.0

# ADDED
- Fix parameter usage in templates, thanks to [Jaapio]
- Introduce source view in default template #2919, thanks to [Jaapio]
- Show var description on properties and constants, thanks to [Jaapio]
- Extend guide descriptor with configuration, thanks to [Jaapio]
- Add help screen to our Make setup, thanks to [Mike van Riel]
- Add source value object in config #2855, thanks to [Jaapio]
- Guides: write test for RenderCommand, thanks to [Mike van Riel]
- Guides: Add more tests, thanks to [Mike van Riel]
- Guides: Complete/introduce versionadded directive, thanks to [Mike van Riel]
- Guides: Remove slug from Environment, clean more up, thanks to [Mike van Riel]
- Guides: automatically determine directives for formats, thanks to [Mike van Riel]
- Add php 8 syntax support, thanks to [Jaapio]
- Guides: make options a firstclass citizen, move to autoloading, thanks to [Mike van Riel]
- Guides: remove old classes and name more inline with the rest, thanks to [Mike van Riel]
- Guides: Remove createSpanNode from Parser, thanks to [Mike van Riel]
- Guides: Refactored NodeFactory and Instantiators away, thanks to [Mike van Riel]
- Guides: clean up more, thanks to [Mike van Riel]
- Guides: refactor last service references from nodes, thanks to [Mike van Riel]
- Move rendering of Guide directives to Twig extension, thanks to [Mike van Riel]
- Guides: remove render functions from twig, thanks to [Mike van Riel]
- Guides: removed unused template parts, thanks to [Mike van Riel]
- Move init of twig to init step #2845, thanks to [Jaapio]
- Simplified node renderers and render paths, thanks to [Mike van Riel]
- Guides: Complete Flyweighting NodeRenderers, thanks to [Mike van Riel]
- Guides: DocumentNodeRenderer and Default renderer needed updating, thanks to [Mike van Riel]
- Guides: Refactor renderers into Flyweights, thanks to [Mike van Riel]
- Update PlantUmlClassDiagram to use new Renderer, thanks to [Mike van Riel]
- Split transformer from template creation, thanks to [Jaapio]
- Style activity diagram and allow for classes, thanks to [Mike van Riel]
- Render UML diagrams using plantuml #2840, thanks to [Mike van Riel]
- Guides: Copy asset when using Image directive, thanks to [Mike van Riel]
- Guides: Render code blocks, thanks to [Mike van Riel]
- Split the compile and transform stage, thanks to [Jaapio]
- Bump phpunit for php8, thanks to [Jaapio]
- introduce VersionSpecification, thanks to [orklah]
- Improve documentation and add Markdown parser, thanks to [Mike van Riel]
- Improve TOC for Guides, thanks to [Mike van Riel]
- try to load the right phpunit version in phive based on php version, thanks to [orklah]
- try to change phive range of version to support php8, thanks to [orklah]
- try to run on php 8, thanks to [orklah]
- reintroduce unused code detection, thanks to [orklah]
- Add test for default value of parameters, thanks to [Jaapio]
- use null coalesce operator when more logical, thanks to [orklah]

# FIXED
- Support UTF-8 in the contents of reST tables #2938, thanks to [David Stächele]
- Add required tag description filters, thanks to [Jaapio]
- Fix constant value desplaying as empty string when zero #2894, thanks to [WhizSid]
- Fix permission issues because of flysystem, thanks to [Jaapio]
- Fix #2832: Determine plantuml location using project dir, thanks to [Mike van Riel]
- Fix #2833 Admonition contents are not rendered, thanks to [Mike van Riel]
- Add symfony/polyfill-intl-idn to fix php 7.4 issue, thanks to [Jaapio]
- Fix interface extends styling, thanks to [Jaapio]
- Remove proxymanager dependency, thanks to [Jaapio]
- Close href in search, thanks to [Jaapio]
- Fix bug in dsn resolving, thanks to [Jaapio]
- Typo fix, thanks to [Jaapio]
- Fix set source, thanks to [Jaapio]
- Fix example notice, thanks to [Jaapio]
- Fix visibility calculation, thanks to [Jaapio]
- Fix notice in tests, thanks to [Jaapio]
- Reimplement filters to use ApiSpecification, thanks to [Jaapio]
- Add validate flag to config, thanks to [Jaapio]
- Wip for api config object, thanks to [Jaapio]
- Leverage newer API of PrettyVersion, thanks to [Alessandro Lai]
- default version should be '1.0.0', not numeric, thanks to [orklah]
- fix #2686 Font Awesome is loaded twice in the HTML template, with conflicting technologies, thanks to [Mike Wilkerson]
- Show Summary and Description for files, thanks to [Mike van Riel]
- Define statements were not parsed as the strategy was not present, thanks to [Mike van Riel]
- Fix #2466 Followup: XML template - description of property empty, thanks to [Jeff Horton]
- Name test sensibly, thanks to [Jeff Horton]
- Fix #2466 - replace twig variables with the updated names - add a functional test for the xml out put containing the expected outputs, thanks to [Jeff Horton]

# DOCS
- Update documentation about template parameters, thanks to [Jaapio]
- Add xsd to docs, thanks to [Jaapio]
- Extend configuration reference, thanks to [Jaapio]
- Adjust the nightly releases documentation in README, thanks to [Nicolas CARPi]
- only build docs on master, thanks to [Jaapio]
- Fix all unresolved links in the documentation, thanks to [Mike van Riel]
- Drop duplicated list item, thanks to [Andrii Dembitskyi]
- Fix typo in document, thanks to [Takuya Aramaki]
- improve configuration doc, thanks to [orklah]
- More progress on the ADR for input and output formats, thanks to [Mike van Riel]
- Start writing an ADR on handling input and output formats, thanks to [Mike van Riel]
- Update installation guide, thanks to [Mike van Riel]
- Remove sphinx from tools, thanks to [Jaapio]
- Build v3 as latest docs, thanks to [Jaapio]
- Docs: improvements for "references - phpdoc - tags - return", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - since", thanks to [jrfnl]
- Docs: "references - phpdoc - tags - param" - remove note about error verification, thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - param", thanks to [jrfnl]

# CI
- Add E2E tests for a Package detail page, thanks to [Mike van Riel]
- Add E2E tests for functions and fix bug, thanks to [Mike van Riel]
- Duplicate groups are not allowed by cypress, add version and os, thanks to [Mike van Riel]
- Enable Cypress retries and recording all E2E tests, thanks to [Mike van Riel]
- Add E2E tests for constants in an interface, thanks to [Mike van Riel]
- Add first set of E2E tests for constants, thanks to [Mike van Riel]
- Add first set of E2E tests for properties, thanks to [Mike van Riel]
- Add E2E tests for methods in interfaces, thanks to [Mike van Riel]
- Split test files into their elements, thanks to [Mike van Riel]
- Rework class overview and add search tests, thanks to [Mike van Riel]
- Reuse sidebar tests in namespace overview and replace smoketests with e2e tests, thanks to [Mike van Riel]
- Divide Cypress tests into two groups and fix psalm/phpstan violations, thanks to [Mike van Riel]
- Add more asserts to Namespace E2E test and setup Cypress Dashboard, thanks to [Mike van Riel]
- Add more E2E tests for the namespace overview, thanks to [Mike van Riel]
- Add E2E tests for the frontpage and extract duplicates, thanks to [Mike van Riel]
- Add E2E tests for the packages and constants on the homepage, thanks to [Mike van Riel]
- Update and add to frontpage tests, thanks to [Mike van Riel]
- Add tests for TagDescriptor namespace, thanks to [Mike van Riel]
- Add matrix setup to smoke test, thanks to [Jaapio]
- Use prophecy via phar install, thanks to [Jaapio]
- Prepare for phpunit 10, thanks to [Jaapio]
- Fix integration test, thanks to [Jaapio]
- psalm lvl 4, thanks to [orklah]
- remove unused variable or mark them as unused, thanks to [orklah]
- Fix e2e tests, thanks to [Jaapio]


2020-10-23: Version 3.0.0

# ADDED

- Allow customization of css, thanks to [Jaapio]
- Filter unresolved interfaces, thanks to [Jaapio]
- Add missing function location to xml template, thanks to [Jaapio]
- Fix issues in structure.xml, thanks to [Jaapio]
- Reintroduce xml structure file, thanks to [Jaapio]
- Add Visibility on ConstantDescriptor and constantAssembler, thanks to [Orklah]
- Improve test for the Template class, thanks to [Mike van Riel]
- Add Files index and make more extendible, thanks to [Mike van Riel]
- Render Collection Types as links, thanks to [Jaapio]
- Add border to default template sidebar, thanks to [Jaapio]
- Disable caching of twig templates and always enable debug, thanks to [Mike van Riel]
- Redesign header, thanks to [Mike van Riel]
- More styling of the default template, and include title in the config xsd, thanks to [Mike van Riel]
- Redesign sidebar / navigation for default template, thanks to [Mike van Riel]
- Add private and protected icons to table of contents, thanks to [Mike van Riel]
- Push updates for table-of-contents, thanks to [Mike van Riel]
- Split templates into smaller templates, thanks to [Mike van Riel]
- Extract TOC entries to separate template, thanks to [Mike van Riel]
- Signature should be styled more according to code, thanks to [Mike van Riel]
- Re-add $ and () to toc entries, thanks to [Mike van Riel]
- Remove prettify from Javascripts, thanks to [Mike van Riel]
- Split methods and properties into sub-templates for re-use, thanks to [Mike van Riel]
- Restructure CSS, thanks to [Mike van Riel]
- Clean up namespaces, packages and index template files, thanks to [Mike van Riel]
- Replace namespace and file templates' contents with components, thanks to [Mike van Riel]
- Add back to top + various changes, thanks to [Mike van Riel]
- Cache-bust assets in demo application, thanks to [Mike van Riel]
- Update screenshot for demo, thanks to [Mike van Riel]
- Integrated RST-Parser into phpDocumentor for faster iterations, thanks to [Mike van Riel]
- [GUIDES] Move more and more dependencies up, thanks to [Mike van Riel]
- Replaced custom error handling with logger for Guides, thanks to [Mike van Riel]
- Refactor custom twig away from Guides and consolidate templates, thanks to [Mike van Riel]
- Refactor Guides to simplify Depenency Injection, thanks to [Mike van Riel]
- [GUIDES] Separate generic from restructuredtext specific code, thanks to [Mike van Riel]
- [GUIDES] Read input from Flysystem, thanks to [Mike van Riel]
- Migrate phpDocumentor\Descriptor\Filter to Prophecy, thanks to [martzd]
- Extract parsing of a file into a separate command and handler, thanks to [Mike van Riel]
- [GUIDES] Use FlySystem for writing output to, thanks to [Mike van Riel]
- [GUIDES] Move parsing to the parser phase, thanks to [Mike van Riel]
- [GUIDES] Separate parsing and rendering even more, thanks to [Mike van Riel]
- [GUIDES] move more services to the front and into the container, thanks to [Mike van Riel]
- [GUIDES] Shuffle code around to simplify dependencies more, thanks to [Mike van Riel]
- [GUIDES] Refactor the Kernel out as to split parsing and rendering, thanks to [Mike van Riel]
- Reintroduce important, note and warning directive and fix paths, thanks to [Mike van Riel]
- Move caching of guides into the pipeline, thanks to [Mike van Riel]
- [GUIDES] URLs between all pages and api documentation work, thanks to [Mike van Riel]
- [GUIDES] Add test for Entry, thanks to [Mike van Riel]
- [GUIDES] Enable Psalm, thanks to [Mike van Riel]
- [GUIDES] Enable phpstan and phpcs checks, thanks to [Mike van Riel]
- [Guides] Enable phpstan, thanks to [Mike van Riel]
- Introduce ApiSetDescriptors, thanks to [Mike van Riel]
- Only build Guides in the RenderGuide writer, thanks to [Mike van Riel]
- Render documentation with phpDocumentor, not Sphinx, thanks to [Mike van Riel]
- Remove build asset step as it is not really used and fails the build, thanks to [Mike van Riel]
- Disable cache for guides, thanks to [Mike van Riel]
- Replace markdown parser with CommonMark, thanks to [Jaapio]

# REMOVED

- Remove redundant functions and add tests, thanks to [Mike van Riel]
- Remove redundant setters and complete test, thanks to [Mike van Riel]
- Remove suggestion to install Twig PHP extension, thanks to [Mike van Riel]
- Remove Behat from the build, thanks to [Mike van Riel]

# FIXED

- Template Default: normalize the section headers, thanks to [jrfnl]
- Fix include of tags in function, thanks to [Jaapio]
- Move search input to top, thanks to [Mike van Riel]
- Resrtructure TOC to use DL instead of table, thanks to [Mike van Riel]
- Moved CSS around and tweaked design of topnav, thanks to [Mike van Riel]
- Move TOC, Breadcrumbs and tags into components, thanks to [Mike van Riel
- Cleanup extension list (origin/php-extensions, php-extensions), thanks to [Jaapio]
- Escape html in markdown, thanks to [Jaapio]
- Improve docblock tag rendering, thanks to [Jaapio]
- ConstantDescriptor can't have FileDescriptor as parent, thanks to [Orklah]
- Fix usage for numeric version number, thanks to [Jaapio]
- Fix version and since tags in default template, thanks to [Jaapio]
- make getTypes never return null values, thanks to [Orklah]
- Ensure that phpDocumentor2 Wildcards are expanded, thanks to [Mike van Riel]
- Fix issue with invalid named magic properties, thanks to [Jaapio]
- Use title and template from v2 config, thanks to [Jaapio]
- Property descriptor is in markdown format, thanks to [Jaapio]
- Internal is additional to the other visibilties, thanks to [Jaapio]
- Remove ext-ctype from suggest because it's already required, thanks to [Randy Geraads]
- XSD: update phpdoc.org URL to https, thanks to [jrfnl]
- Fixes #2442: show 'mixed' in TOC for properties without type, thanks to [Mike van Riel]
- Constant values in TOC should be singleline, thanks to [Mike van Riel]
- Constants should not show a dollar sign in signature, thanks to [Mike van Riel]
- Correctly render links to FQSEN references, thanks to [Mike van Riel]
- Property default value of 0 is shown as an empty string, thanks to [Mike van Riel]
- Prevent duplicate methods, properties and constants, thanks to [Mike van Riel]
- Add deeplinks and tweak grid, thanks to [Mike van Riel]
- Simplify the grid classes and match standards, thanks to [Mike van Riel]
- Fixes #2457: Do not show global namespace when empty, thanks to [Mike van Riel]
- Various changes to the default template, thanks to [Mike van Riel]
- Remove slashes from default values, thanks to [Jaapio]
- Revert added slashes to constant value, thanks to [Jaapio]
- Fix graph path, thanks to [Jaapio]
- Fix issues after parsing vendor dir, thanks to [Jaapio]
- fix syntax error on base template, thanks to [Jaapio]
- Improve link rendering for list like types, thanks to [Jaapio]
- Fix display of return type and param description, thanks to [Jaapio]
- prevent PHP error. Reason: type parameter for magic property can be null, thanks to [Axel Krysztofiak]
- fixed phpstan error, thanks to [Randy Geraads]
- Partially fix strip inline tags, thanks to [Jaapio]
- Add testcase for issue #2425, thanks to [Jaapio]
- Allow null descriptions, thanks to [Jaapio]
- Update CSS with missing parts from website and update docs, thanks to [Mike van Riel]
- Update demo site's styling, thanks to [Mike van Riel]
- Fix link rendering, thanks to [Jaapio]
- Fix namespace defined constants (origin/fix-namespace-constants), thanks to [Jaapio]
- Apply short notation of elements by default, thanks to [Jaapio]
- Fixes duplicated logs, thanks to [Jaapio]
- Ensure traits are part of a namespace, thanks to [Jaapio]
- Add builder to reducers, thanks to [Jaapio]
- Fix check for reducer injection, thanks to [Jaapio]
- Reintroduce examples in config and cli, thanks to [Jaapio]
- Losen xsd order, thanks to [Jaapio]
- [GUIDES] Change url linking to make menu work better, thanks to [Mike van Riel]
- Ensure guides are rendered in the correct location, thanks to [Mike van Riel]
- Extend list of default config file names, thanks to [Jaapio]
- Allow multiple visibilities, thanks to [Jaapio]
- Remove manditory paths setting, thanks to [Jaapio]
- Fix filter constants, thanks to [Jaapio]
- Add tests for visiblity, thanks to [Jaapio]
- Fix commandline argument ignore-tags, thanks to [Jaapio]
- Update our own config to ignore tags, thanks to [Jaapio]
- Add strip tags filter, thanks to [Jaapio]
- Fix configuration loading for ignore-tags, thanks to [Jaapio]
- Improve creation of element filters, thanks to [Jaapio]
- Include blockquote and fix height in navbar, thanks to [Mike van Riel]
- GH#2493 Search links do not resolve on the homepage, thanks to [Mike van Riel]
- Default template does not have responsiveness, thanks to [Mike van Riel]
- Tweak navigation, thanks to [Mike van Riel]
- Removed event dispatching from Node and wrote test, thanks to [Mike van Riel]
- Add default for null values, thanks to [Jaapio]
- Resolve style issues (origin/bugfix/2493), thanks to [Mike van Riel]
- Resolve last issues in default template, thanks to [Mike van Riel]

# Security

- Update Cypress to resolve security issues, thanks to [Mike van Riel]
- Updated http-foundation to resolve reported security issues, thanks to [Mike van Riel]

# DOCS

- Improve layout of comment after "your environment", thanks to [HonkingGoose]
- Improve bug report template, thanks to [HonkingGoose]]
- Add whitespace for readability, thanks to [HonkingGoose]
- Use proper casing for the word GitHub, thanks to [HonkingGoose]
- Update README.md, thanks to [HonkingGoose]
- Cleanup docs, thanks to [Jaapio]
- Add filesystem docs, thanks to [Jaapio]
- Fix even more links in docs, thanks to [Mike van Riel]
- Fix documentation links, thanks to [Mike van Riel]
- Fix typos in docs, thanks to [Mike van Riel]
- Improve FileDescriptor docs, thanks to [Jaapio]
- Add tags to improve api docs for templates, thanks to [Jaapio]
- Add hint about plaintext summaries, thanks to [Jaapio]
- Small doc improvements, thanks to [Jaapio]
- Removed all 'under construction' pages, thanks to [Mike van Riel]
- Docs: improvements for "getting started - changing the look and feel", thanks to [jrfnl]
- Docs: further improvements for "getting started - installing", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - internal", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - version", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - var", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - todo", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - throws", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - subpackage", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - see", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - filesource", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - deprecated", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - copyright", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - category", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - package", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - link", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - license", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - ignore", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - author", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - api", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - property[<-read|-write>]", thanks to [jrfnl]
- Docs: improvements for "getting started - your first set of documentation", thanks to [jrfnl]
- Docs: improvements for "references - phpdoc - tags - method", thanks to [jrfnl]
- Docs: improvements for "getting started - installing", thanks to [jrfnl]
- Docs: fix various links [public facing], thanks to [jrfnl]
- Fix references to the phpdoc.org website [test fixtures], thanks to [jrfnl]
- Fix references to the phpdoc.org website [test file docblocks], thanks to [jrfnl]
- Fix references to the phpdoc.org website [src file docblocks], thanks to [jrfnl]
- Fix references to the phpdoc.org website [templates], thanks to [jrfnl]
- Fix references to the phpdoc.org website [docs website], thanks to [jrfnl]
- Fix references to the phpdoc.org website [public facing], thanks to [jrfnl]
- Docs: remove `:term:` annotations, thanks to [jrfnl]
- Inline docs: php.net - use language agnostic links and more, thanks to [jrfnl]
- Docs: php.net - use language agnostic links and more, thanks to [jrfnl]
- Docs: include instead of duplicate for "index", thanks to [jrfnl]
- Docs: consistency in referring to the project, thanks to [jrfnl]
- Template files: remove a broken link, thanks to [jrfnl]
- Template files: fix various links, thanks to [jrfnl]
- add option for creating class diagram in the docs, thanks to [Subhojit Paul]
- Fix internal tag filtering, thanks to [Jaapio]
- Fix covers annotations, thanks to [Jaapio]
- Fix more tests and example rendering, thanks to [Jaapio]
- Advanced inline tag rendering, thanks to [Jaapio]
- Fix all descriptor tests, thanks to [Jaapio]
- Expirment with description filter, thanks to [Jaapio]
- Disable compiler passes right now, thanks to [Jaapio]
- Switch to Description object, thanks to [Jaapio]
- Add documentation for custom settings, thanks to [Mike van Riel]
- Update documentation for guides, thanks to [Mike van Riel]
- Update readme to prepare for v3, thanks to [Jaapio]


# CI

- Composer v2.0: remove `--no-suggest flag` as its going to be deprecated, thanks to [HonkingGoose]
- Add Docker to .dependabot/config.yml, thanks to [HonkingGoose]
- removed unused imports, thanks to [Randy Geraads]
- Add test for ProvideTemplateOverridePathMiddleware, thanks to [Mike van Riel]
- Improve ci cache, thanks to [Jaap van Otterdijk]
- Add unittest for flysystem loader, thanks to [Jaapio]
- Add npm to dependabot, thanks to [Jaapio]
- Add checker for broken doc links, thanks to [Jaapio]
- Add psalm to phive, thanks to [Jaapio]
- Improve ci pipeline setup, thanks to [Jaapio]
- add strict_types when missing, thanks to [Orklah]
- add param types where possible, thanks to [Orklah]
- small fixes. Preparation for Psalm, thanks to [Orklah]
- psalm improvements, thanks to [Orklah]
- Migrate phpDocumentor\{Parser, Event, Pipeline} from Mockery to Prophecy, thanks to [simivar]
- Migrate phpDocumentor\Transformer from Mockery to Prophecy, thanks to [simivar]
- CI: allow Cypress tests to pass, thanks to [jrfnl]
- PHPCS config: minor tweaks, thanks to [jrfnl]
- fix psalm issues on last version, thanks to [Orklah]
- ignore TooManyTemplate error on Compiler because Psalm doesn't yet expect template annotation to SplPriorityQueue, thanks to [Orklah]
- add template annotations on Compiler, thanks to [Orklah]
- ignore phpstan false-positive until the bugfix, thanks to [Randy Geraads]
- Command to pull the latest docker containers used in the Makefile, thanks to [Randy Geraads]
- File docblocks: remove license tags, thanks to [jrfnl]
- Bump release flow to phar action (origin/phar-action, phar-action), thanks to [Jaapio]
- Start using phar github action, thanks to [Jaapio]
- Fix composer.lock after update, thanks to [Jaapio]
- Bump to composer 2, thanks to [Jaapio]
- Publish NPM package with assets, thanks to [Mike van Riel]
- Invalid breakpoint used, causing CSS to go wonky, thanks to [Mike van Riel]
- Re-enable windows testing, thanks to [Jaapio]
- Enable github-actions updates via Dependabot, thanks to [HonkingGoose]
- Migrate phpDocumentor\Compiler\Pass from Mockery to Prophecy, thanks to [simivar]
- Migrate phpDocumentor\Console from Mockery to Prophecy, thanks to [simivar]
- Migrate test cases that don't use Mockery to PHPUnit's TestCase from MockeryTestCase, thanks to [simivar]
- Add unit tests for dns, thanks to [Marvin Caspar]
- Psalm.xml simplification, thanks to [orklah]
- Migrate phpDocumentor\Descriptor\Builder\Reflector\Tags to Prophecy (#2609), thanks to [martzd]
- Migrate phpDocumentor\Descriptor\Builder\Reflector to Prophecy (#2607), thanks to [martzd]
- add details in phpdoc, thanks to [orklah]
- redundant phpdoc, thanks to [orklah]
- redundant default parameter value, thanks to [orklah]
- redundant property initialization, thanks to [orklah]
- unused variables, thanks to [orklah]
- use Assert lib instead of native assert, thanks to [orklah]
- create a MiddlewareInterface and normalize Middlewares, thanks to [orklah]
- push psalm to level 6, thanks to [orklah]
- Phpstan doesn't support inheritance of templates + fix various issues, thanks to [orklah]
- changing templates for coherence, thanks to [orklah]
- replace static by self and replace className by self when applicable, thanks to [orklah]
- Rebuild docs when template changes, thanks to [Mike van Riel]


2020-02-09: Version 3.0.0-rc

# Removed
- Self update command thanks to [Jaapio]

# FIXED
- Docblock errors are now visible in default template, #2250 thanks to [Jaapio]
- Run doesn't ignore cache with `--force`, #2257 thanks to [Mike van Riel]
- `--config=none` doesn't respect commandline arguments thanks to [Jaapio]
- Tag bodies starting with `[` are not accepted, #2260 thanks to [Jaapio]
- Files starting with shebang are not parsed, #2259 thanks to [Jaapio]
- Inline markers are not detected, #2256 thanks to [Jaapio]
- Files with non utf-8 encoding are not processed correctly, #2254 [Jaapio]

# CI
 - Bring PHPStan to lvl 6 thanks to [Orklah]
 - Internals are better typed thanks to [Orklah]
 - Upgrade Coding standards to phpdoc coding standards thanks to [Jaapio]

2020-01-31: Version 3.0.0-beta

# BREAKING CHANGES
 -  Minimum PHP version to run this is 7.2 thanks to [Mike van Riel]
 -  Trunk based development thanks to [Mike van Riel]
 -  Support for XML-based templates was dropped thanks to [Mike van Riel]
 -  Support for rendering an XML-based ast (structure.xml) was dropped thanks to [Mike van Riel]

# ADDED
 -  Add new default template thanks to [Mike van Riel]
 -  Add reports to the new template thanks to [Mike van Riel]
 -  Add missing visibilities thanks to [Mike van Riel]
 -  Add namespace and class-based templates for new default theme thanks to [Mike van Riel]
 -  Adds dockerfile for docs thanks to [Jaapio]
 -  Add Search and a lot of little features to the new template thanks to [Mike van Riel]
 -  Add support for 'deprecated' and test various method modifiers thanks to [Mike van Riel]
 -  Add support for Typed Properties thanks to [Mike van Riel]
 -  Add support for Version 2 configuration and make an upgrade mechanism thanks to [Mike van Riel]
 -  Add VERSION to documentation build thanks to [Mike van Riel]
 -  Allow use of 'local' templates thanks to [Mike van Riel]
 -  Cleaned up dependency versions in Composer thanks to [Mike van Riel]
 -  Ensure Cache Folder is O/S neutral thanks to [Mike van Riel]
 -  Rework File I/O for Writers and Transformations thanks to [Mike van Riel]
 -  Custom Settings may be set from command line or config thanks to [Mike van Riel]
 -  Flesh out new Class Details page thanks to [Mike van Riel]
 -  Improve handling of constants in new template thanks to [Mike van Riel]
 -  Improve new default template thanks to [Mike van Riel]
 -  Improve rendering of properties in the new template thanks to [Mike van Riel]
 -  Initial setup for new template thanks to [Mike van Riel]
 -  Introduce FlySystem into twig file loading thanks to [Mike van Riel]
 -  Introduce Symfony Config for configuration parsing thanks to [Mike van Riel]
 -  Writers should be able to expose custom settings thanks to [Mike van Riel]
 -  Recursively upgrade configuration to newer versions thanks to [Mike van Riel]
 -  Render files as part of new template and link to them thanks to [Mike van Riel]


# FIXED
 -  Building an '@package' tree failed with certain characters thanks to [Mike van Riel]
 -  Clean run command docs thanks to [Jaapio]
 -  Clean up file header for new config thanks to [Mike van Riel]
 -  Collections were always replaced, even when unmodified thanks to [Mike van Riel]
 -  Convert v2 config to v3 and make v3 similar to previous thanks to [Mike van Riel]
 -  Allow comma-separated values for `-f` thanks to [Mike van Riel]
 -  Allow default symfony command execution thanks to [Jaapio]
 -  Allow definition of multiple api's thanks to [Jaapio]
 -  Allow for output folders to be set thanks to [Mike van Riel]
 -  Allow multiple directories using comma's thanks to [Mike van Riel]
 -  Fix string class names in tests thanks to [50bhan]
 -  Configuration needed a 'phpdocumentor' root element thanks to [Mike van Riel]
 -  Conversion of slashes causes issues instead of fixing it thanks to [Mike van Riel]
 -  Convert URIs into League URIs thanks to [Mike van Riel]
 -  Dockerfil was missing && thanks to [Mike van Riel]
 -  Ensure that 'filename' and 'directory' is properly checked thanks to [Mike van Riel]
 -  Ensure that the config/secrets/prod folder exists thanks to [Mike van Riel]
 -  Correctly render links to Nullable types thanks to [Mike van Riel]
 -  hotfix/docker-build #1976 thanks to [Jaap van Otterdijk]
 -  More robust vendor directory detection #2153 thanks to [jclaveau]
 -  Replace Cocur/Slugify with Symfony/String #2165 thanks to [Mike van Riel]
 -  relative path resolving #2193 thanks to [Jaap van Otterdijk]
 -  resolve target path #2196 thanks to [Jaap van Otterdijk]
 -  windows path issue #2197 thanks to [Jaap van Otterdijk]
 -  windows template path resolving #2202 thanks to [Jaap van Otterdijk]
 -  Invalid tags processing #2209, #2205 thanks to [Jaap van Otterdijk]
 -  Finish configuration definition for v2 thanks to [Mike van Riel]
 -  Have the Twig writer write using FlySystem thanks to [Mike van Riel]
 -  Upgrade to Symfony 5 thanks to [Mike van Riel
 -  Improve error message during get magic props thanks to [Jaapio]
 -  Improve error message on invalid path thanks to [Jaapio]
 -  Create more domain specific exceptions thanks to [Jaapio]
 -  Fragments cannot be expressed in a route thanks to [Mike van Riel]
 -  Monolog started complaining about a setting in their latest version thanks to [Mike van Riel]
 -  Passing "--config none" throws error thanks to [Mike van Riel]
 -  phpDocumentor goes into infinite loop when class extends itself thanks to [Mike van Riel]
 -  Normalize package names thanks to [Mike van Riel]
 -  Links are not rendered to the correct location thanks to [Mike van Riel]
 -  Show quotes with string values thanks to [Mike van Riel]
 -  Unresolved types could not be rendered thanks to [Mike van Riel]

# REMOVED
 -  Remove validation of DocBlocks
 -  Remove compression on phar thanks to [Jaapio]
 -  Remove default language and set per job thanks to [Mike van Riel]
 -  Removed Travis and Appveyor since Github Actions does all thanks to [Mike van Riel]
 -  Remove duplicate :bug icon thanks to [Jaapio]
 -  Remove duplicate entry from the build pipeline thanks to [Mike van Riel]
 -  Removed Zend Serialiser and StdLib thanks to [Mike van Riel]
 -  Remove OS dependence on vendor folder thanks to [Mike van Riel]
 -  Remove our own Uri object thanks to [Mike van Riel]
 -  Remove Parse and TransformCommand thanks to [Mike van Riel]
 -  Remove pr and lable from issue workflow thanks to [Jaapio]
 -  Remove Type descriptors thanks to [Mike van Riel]
 -  Remove unused exception classes thanks to [Mike van Riel]
 -  Remove unused exception classes thanks to [Mike van Riel]
 -  Remove unused function and test thanks to [Mike van Riel]
 -  Remove unused interface thanks to [Mike van Riel]
 -  Remove unused property thanks to [Mike van Riel]
 -  Remove unused settings, fix linting and one test thanks to [Mike van Riel]
 -  Remove workaround for generating projects in other folders thanks to [Mike van Riel]


# DOCS
 -  Ensure that all 404's are redirect to /latest thanks to [Mike van Riel]
 -  Ensure that the docroot always goes to /latest thanks to [Mike van Riel]
 -  Fixed link to Graphviz thanks to [Mike van Riel]
 -  Update CONTRIBUTING.md with new branches and repo names thanks to [Mike van Riel]
 -  Update documentation on how Configuration works thanks to [Mike van Riel]
 -  Update documentation template to match new style thanks to [Mike van Riel]
 -  Update README with a couple of links thanks to [Mike van Riel]
 -  Update shields thanks to [Mike van Riel]
 -  Use default nginx image and allow versioning thanks to [Mike van Riel]
 -  Improve installation descriptions thanks to [Jaapio]
 -  Improve run command docs thanks to [Jaapio]
 -  Readme update thanks to [Jaapio]

# CI
 - Fix some errors on PHPStan lvl 6 thanks to [Orklah]
 - Fixed indentation thanks to [Graham Campbell]
 - Raised test coverage from 35% to 70% thanks to [Jaapio] & [Mike van Riel]
 - Add dependabot automerge thanks to [Jaapio]
 - Add unit testing to Github Actions pipeline thanks to [Mike van Riel]
 - Add working lables for actions and packagist thanks to [Jaapio]
 - Add PHAR building to Behat PHAR tests thanks to [Mike van Riel]
 - Add a quick check for code coverage thanks to [Mike van Riel]
 - Add a smoke test stage thanks to [Mike van Riel]
 - Add initial e2e test for Default template thanks to [Mike van Riel]
 - Add E2E tests for the Class details of Default thanks to [Mike van Riel]
 - Add behat as binary thanks to [Jaapio]
 - Add behat to execute thanks to [Jaapio]
 - Add bin folder to cache thanks to [Jaapio]
 - Add build matrix on unit tests thanks to [Mike van Riel]
 - Add convenience binaries for tools thanks to [Mike van Riel]
 - Add extra comment with cache key determination thanks to [Mike van Riel]
 - Add make target for setting up phive and fix tests thanks to [Mike van Riel]
 - Add missing symbols to require checker and check on pre-commit thanks to [Mike van Riel]
 - Add support for testing the clean template thanks to [Mike van Riel]
 - Add Symfony's var-dump-server binary to the gitignore thanks to [Mike van Riel]
 - Add issue templates thanks to [Jaapio]
 - Build documentation using Github Actions thanks to [Mike van Riel]
 - Bump phpstan to 0.12 thanks to [Jaapio]
 - Change caching of build tools thanks to [Mike van Riel]
 - Change output folders for tests thanks to [Mike van Riel]
 - Use the PHAR of PHPUnit instead of installing it globally thanks to [Mike van Riel]
 - Enable the behat tests for all environments thanks to [Mike van Riel]
 - Download tools in setup step and cache it thanks to [Mike van Riel]
 - Update issue triage workflow thanks to [Jaapio]
 - Improve build workflow with Behat tests thanks to [Mike van Riel]
 - Switch from xdebug to pcov for Code Coverage thanks to [Mike van Riel]
 - Move example project to examples and include Cypress in build thanks to [Mike van Riel]
 - Update Cypress tests to include latest changes in the template thanks to [Mike van Riel]
 - Include Windows and MacOSX builds thanks to [Mike van Riel]
 - PHPStan Lvl4 thanks to [Mike van Riel]
 -  Merge pull request #2187 from phpDocumentor/fix/phpstan-level-5 thanks to [Jaap van Otterdijk]
 -  Merge pull request #2141 from phpDocumentor/tighten-up-coding-standards thanks to [Jaap van Otterdijk]
 -  Merge pull request #2134 from phpDocumentor/feature/phpunit8 thanks to [Jaap van Otterdijk]
 -  Merge pull request #2139 from phpDocumentor/feature/php74 thanks to [Jaap van Otterdijk]
 -  Simplify Behat build matrix and improve labels thanks to [Mike van Riel]
 -  Simplify Docker setup thanks to [Mike van Riel]
 -  Simplify installation of phive and add ocular thanks to [Mike van Riel]
 -  Persist PHAR as artifact and re-use it where possible thanks to [Mike van Riel]
 -  Run Cypress/E2E tests against all major O/S thanks to [Mike van Riel]
 -  Run Cypress on 16.04 due to a bug in 18.04/Cypress 3.8.2 thanks to [Mike van Riel]
 -  Split phpunit into a fast version and a build matrix version thanks to [Mike van Riel]




 
[Mike van Riel]: https://github.com/mvriel 
[Jaapio]: https://github.com/jaapio 
[jrfnl]: https://github.com/jrfnl 
[Jaap van Otterdijk]: https://github.com/jaapio 
[jclaveau]: https://github.com/jclaveau 

```
DROP:      Support symlinks
```

2014-11-23: Version 2.8.2
-------------------------

```
FIXED:     Self-update for PHAR files
FIXED:     Log now shows which elements do not have a summary
FIXED:     When omitting markers the code now automatically picks TODO and FIXME
FIXED:     Missing assets in new-black and abstract template
FIXED:     phpDocumentor will error if the iconv extension is missing.
```

2014-11-13: Version 2.8.1
-------------------------

```
FIXED:     Fatal error in phar file when used from a folder containing a composer.json
```

2014-10-29: Version 2.8.0
-------------------------

```
ADDED:     Argument "--cache-folder" to indicate where the cache is stored
ADDED:     `phpdoc self-update` command to the PHAR archive to update phpDocumentor with a single command
FIXED:     #423: Error report in responsive and responsive-twig template should hide empty results and update error
           counter next to the filename.
FIXED:     #573: Visibility filters in 'responsive' and 'responsive-twig' do not function properly
FIXED:     Several scrutinizer reported clean ups
FIXED:     If tmp was somewhere in path it would be incorrectly replaced
FIXED:     Several tests were added
FIXED:     It is now possible to install phpDocumentor using composer in a project using ZF 2.3 or higher
FIXED:     Fatal error: Call to a member function getParent() on a non-object in ConstantDescriptor.php
FIXED:     Fatal error: Call to a member function getParent() on a non-object in PropertyDescriptor.php
REMOVED:   Knp menu from composer.json because it was not used
```

2014/08/18: Version 2.7.0
-------------------------

```
ADDED:     A new writer that outputs a statistical extract from the collected data
ADDED:     Windows support for Ansible playbooks
ADDED:     8% to 10% Code Coverage thanks to #testfest 2014
ADDED:     #1347: Support for custom Vendor folders
ADDED:     Plugins can now be configured using parameters.
ADDED:     Complete support for @example
ADDED:     NamespacePrefix to LegacyNamespaceConverter plugin
FIXED:     Fatal error in MethodDescriptor when a parent was incorrectly called
FIXED:     Notice in '@see' handling
FIXED:     #1349: Configuration file was not found in working directory
FIXED:     Phing integration by re-instating the bootstrap class
FIXED:     Fixing type inference and variable length issues
FIXED:     Whether a method is inherited is shown again in XSL-based templates
FIXED:     Various minor bugs that became visible during the writing of tests
FIXED:     #1390: Source code paths were incorrect if the source was in folder
FIXED:     #1341: Icons in clean template were shown incorrectly
FIXED:     #1331: Not all validations were properly shown
FIXED:     #1077: Spaces in a path won't trip up libxml anymore
CHANGED:   Changed reference to deprecated Parsedown method parse() to text()
CHANGED:   Completely replaced Puppet with Ansible to provision contributor VMs
CHANGED:   Simpler provisioning for generic contributors; to do profiling
           another task is now needed
CHANGED:   Docs no longer refer to Short Description or Long Description but to
           Summary and Description per PSR-5
```

2014/07/09: Version 2.6.1
-------------------------

```
FIXED:     #1330: Fix crash when assembling package tags
FIXED:     #1326: Fix crash while generating routes in Twig templates
```

2014/06/27: Version 2.6.0
-------------------------

```
ADDED:     #1087: Fully support `@see`
ADDED:     #1213: Resolve inline @see and @link to Markdown link
CHANGED:   #1186: Move checking of writer requirements
CHANGED:   #1267: XSL should use Router
CHANGED:   Update contribution guide to match new XHGUI
CHANGED:   #1248: Replace ZendConfig with Serializer-based config
CHANGED:   #1017: Updated help output for --hidden to be more descriptive
CHANGED:   #1264: Clean template no longer requires an internet connection
CHANGED:   #1212: Plugin configuration can now be loaded from a config file
CHANGED:   #1194: Add support for default parameter values in @method
FIXED:     #1313: Namespace Aliases are not stored on FileDescriptor
FIXED:     #1308: Multiple templates in configuration do not work
FIXED:     #713: XSL Templates are not found in Windows
FIXED:     #1253: Transformation crashes on typehint
FIXED:     #1268: Restore handling of visibility
FIXED:     #1130: Arrays not resolved in documentation
FIXED:     #1278: Template is not read from configuration
FIXED:     #1239: Parseprivate always triggers a complete parsing run
FIXED:     Parsing crashes on visibility as string
FIXED:     Routing crashes in Twig
FIXED:     #1307: Parsing crashes on unknown trait
FIXED:     #1114: Debug mode for Twig was not enabled
FIXED:     Errors were not shown in XSL templates
FIXED:     Remove duplicate namespace separator in constants defined by DEFINE
FIXED:     #930: @see and @link did not resolve self
FIXED:     #993: UTF-8 characters in filenames do not work in windows
FIXED:     #790: Inheritance in XSL was incorrectly registered
FIXED:     #713: Phar was sometimes not working on windows due to paths
FIXED:     Restore Behat tests
FIXED:     #1252: Namespaces are not shown on responsive template
```

2014/05/17: Version 2.5.0
-------------------------

```
FIXED:     #1211: Loading a single plugin is not possible
FIXED:     #1232: Routing crashes on magic property in trait
FIXED:     #157: Classes do not inherit trait methods and props
FIXED:     #1193: Package tags don't inherit to classes
FIXED:     #1229: @method tag in a trait causes Exception
FIXED:     #1196: Some files cannot be copied with FileIO
CHANGED:   Better OPcache handling, annotations are not stripped anymore in PHP 5.5+
CHANGED:   phpdoc.php was renamed to phpdoc; phpdoc.php is kept for backwards compatibility
```

2014/04/01: Version 2.4.0
-------------------------

```
FIXED:     #1141: Deprecated report was missing in Clean template.
FIXED:     #1191: Opcache comments were not disabled due to incorrect extension name
FIXED:     #1184, #1181: @package tag on File was not recognized and not inherited on children
FIXED:     #1180: phpDocumentor crashes if an `@subpackage` has no name set
FIXED:     #1178: Generating a marker listing crashes sometimes
FIXED:     #1176: DocBlocks were not overridden on subclasses
FIXED:     #1163: Responsive and Responsive-twig templates do not work on HTTPS
FIXED:     #1158: Clean and Responsive template shows empty namespace menu when there are no namespaces
FIXED:     #1134, #1132: GraphViz reports can crash due to empty labels
FIXED:     #1098: Re-added missing Javascript file
FIXED:     #1037: IE did not like empty anchors
FIXED:     #152:  @internal and @ignore did not behave as expected
FIXED:     Javascript notices in Clean by upgrading jQuery
ADDED:     #1141: Deprecated report to the Clean template
ADDED:     #629:  Support for Variadics
CHANGED:   #1099, #1101: Replaced custom PHAR compiling with box-project.org and fixing issues in the mean time
```

2014/03/07: Version 2.3.2
-------------------------

```
FIXED:     #1133: Fixed subpackage without package validation
FIXED:     #1120: Set stable versions of DOMPDF and ezcDocument in Composer.json
```

2014/02/26: Version 2.3.1
-------------------------

```
CHANGED:   #1128: Minimum Symfony/Console version was set at 2.3 to support LTS version.
FIXED:     #1090: There were still GraphViz issues, these have now been fixed and confirmed.
FIXED:     #1131: Fatal Error on ConstantConverter when using an XML-based template.
```

2014/02/16: Version 2.3
-----------------------

```
ADDED:     Support for writing PDF files using a Twig template by adding the PDF writer.
ADDED:     More documentation on types, running phpDocumentor, the @var tag.
ADDED:     Support for sorting lists with Twig and XSLT.
ADDED:     Basic support for generating reference documentation with ReST documents.
CHANGED:   Replaced custom phar building with the library from https://box-project.github.io/.
FIXED:     #1090: GraphViz issues where the global namespace caused GraphViz not to generate.
FIXED:     #1037 by @siad007: if opcode cache is enabled for CLI then annotations are dropped.
FIXED:     #1031: Fixed inheritance for the summary, description, tags and the way @inheritdoc works.
FIXED:     Adding several unit tests.
FIXED:     Cleaned up code to remove warnings and errors.
FIXED:     #1111: An infinite loop occurred during processing of global constants.
FIXED:     Crash that occurred when building a tree of packages.
FIXED:     Crash that occurred when an interface was not recognized.
FIXED:     XSLTCache extension could not be used instead of XSLTProcessor due to a stray typehint.
FIXED:     #1110: Crash on incomplete `define` definition.
FIXED:     #949: `@inheritdoc` is not working with interfaces.
```

2013/11/23: Version 2.2
-----------------------

```
ADDED:     Information how to donate to phpDocumentor using gittip
ADDED:     Profiling information and process
CHANGED:   Moved the external class documentation code to their own router
CHANGED:   #1080: Replaced MarkDownExtra dependency with Parsedown
CHANGED:   Rewrote a lot of the documentation; still a work in progress
FIXED:     #720: External class documentation had ../files prepended
FIXED:     #932: Todo tags did not show up in Twig templates
FIXED:     #1049: Arguments passed by reference did not show up as being by reference
FIXED:     #1075: Prevent output from wrapping in Windows console
FIXED:     Cleaned up code based on Continuous Integration messages
FIXED:     Timezone issues on some machines
FIXED:     Various issues in several templates
```

2013/09/28: Version 2.1
-----------------------

```
ADDED:     New cli option 'log' to tell phpDocumentor the path where to log to
ADDED:     Verbosity can now be provided in three level, each indicating how much is written in the log
ADDED:     Support for the XSLCache drop in replacement of ext/xsl
ADDED:     Locale can now be supplied in the configuration
ADDED:     German translation for error messages
ADDED:     Support to insert Markdown files into specific points of the outputted documentation (partials)
ADDED:     Deprecated tag now registers the version number since when the associated element was deprecated
ADDED:     Show which traits are consumed by a class
ADDED:     Link to the traits that are consumed by a class
ADDED:     Show traits with their methods and properties
CHANGED:   phpDocumentor now checks for transformation requirements at the start of the application (#148)
FIXED:     Fatal error with some of the old-style validations
FIXED:     Template:list command errors due to an incorrect path
FIXED:     Checkstyle error report only showed errors of the file itself and not subelements (#1046)
FIXED:     Validation to check for validity of return types
FIXED:     Validation to check for validity of parameters
FIXED:     Fatal error when a parent interface is not in the project
FIXED:     Template:list does not throw an error
FIXED:     XML output correctly exposes @see, @link, @version
FIXED:     XML output now does not choke on special characters
FIXED:     Error code for return type was incorrect
FIXED:     Functions in the responsive twig now unfold to show complete contents
FIXED:     Constants in the responsive twig now unfold to show complete contents
FIXED:     Using an ampersand in the type of an @param no longer causes a fatal (not a recommended practice!)
REMOVED:   #814; removed ParserAbstract base class for Parser
REMOVED:   PSR Draft is now moved to its own repo: https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md
REMOVED:   Automatic generation of log files, the new log option can be used or a configuration option
```

2013/08/08: Version 2.0.1
-------------------------

```
FIXED:      Generated phar files could not be set to executable and ran.
FIXED:      Missing File-level DocBlocks were not detected.
FIXED:      Classes and namespace were not generated in Windows.
FIXED:      Notices were thrown with the Clean template.
FIXED:      In windows were path calculated incorrectly.
```

2013/08/03: Version 2.0.0
-------------------------

```
ADDED:      Presentation mode to router Twig filter, allows for different representations
ADDED:      StandardRouter now also supports generating paths for file documentation pages.
ADDED:      Version tag is now processed in the descriptors
FIXED:      Generated paths in the Twig writer were not windows safe
FIXED:      Minor Descriptor tweaks
FIXED:      Inheritance was broken due to an erroneous merge
FIXED:      Ampersands in tag descriptions caused XSL based templates to fatal
FIXED:      Inheritance of methods in interfaces was broken
FIXED:      All elements had an internal and ignore tag added due to an error in filtering
FIXED:      @internal inline tag handling did not function
FIXED:      Fatal error when an argument in an @method tag does not have a type
FIXED:      The logging directives in the configuration file were not followed.
CHANGED:    When installing composer the template folder will be vendor/phpdocumentor/templates and not data/templates
CHANGED:    The included ProgressHelper was replaced with the new ProgressHelper of Symfony Console
            Components (https://github.com/symfony/symfony/pull/3501).
REMOVED:    Installer is removed; proved too unreliable
DEPRECATED: Previously shared assets could be in the /data folder; this unnecessarily complicated template handling
            and composer integration. Shared assets have now been moved inside the templates and when a template
            requests shared assets it is in fact requesting files from the abstract template.
            Using a source attribute with a transformation that has no direct reference to a template will be removed
            in version 3.0; until that point the code will trigger E_USER_DEPRECATED warnings.
```

2013/07/12: Version 2.0.0b7
---------------------------

```
FIXED:     Warning in browser console 'Viewport argument value "device-width;" for key "width" is invalid, and has
           been ignored. Note that ';' is not a separator in viewport values. The list should be comma-separated.'
FIXED:     VERSION file was missing from phar archive, causing it to fail
FIXED:     Elements with an @ignore tag were not filtered
FIXED:     Deprecated elements are now striken in the class overview
FIXED:     The @see reference was not shown and interpreted
FIXED:     The @uses reference was not shown and interpreted
FIXED:     Response type was not shown with magic methods
FIXED:     Arguments were not shown with magic methods
FIXED:     Type is not shown with magic properties
FIXED:     Magic methods were missing from sidebar
FIXED:     Coding standards issues
FIXED:     Several documentation issues
FIXED:     Windows error where the directory slashes were incorrectly presented.
FIXED:     When a file contains multiple errors, only the first is shown.
FIXED:     Generating a new template gave a fatal error
FIXED:     Generated templates were missing the transformation line for their structure.xml
FIXED:     Linking to functions
FIXED:     Linking to constants
FIXED:     Linking to properties
FIXED:     Linking to methods
FIXED:     Root elements with the same shortname and file but in a different namespace would overwrite the other.
ADDED:     New template 'xml' for outputting a pure XML representation of the project's structure
ADDED:     Update instructions to issue a PR against homebrew if the phar file updates:
           https://github.com/josegonzalez/homebrew-php/blob/master/Formula/phpdocumentor.rb
CHANGED:   Assembling of Tags to Descriptors was refactored to work the same way as other Descriptors.
CHANGED:   Properties won't emit an error regarding a missing summary if it has an @var tag with a description.
```

2013/06/23: Version 2.0.0b6
---------------------------

```
ADDED:     Travis configuration was changed to test against 5.5
FIXED:     Packages were not indexed and shown correctly.
FIXED:     @var descriptions were not shown as summary if the summary was absent for a property.
FIXED:     Added static label on a property in the responsive template.
FIXED:     Alignment of tags in table display.
FIXED:     Response information was missing from method description.
FIXED:     Sourcecode viewer in new-black template.
FIXED:     Magic methods are now shown and inherited in the class view for the responsive-twig template.
FIXED:     Magic properties are now shown and inherited in the class view for the responsive-twig template.
FIXED:     Markdown fencing in responsive and responsive-twig template now correctly indents code examples.
CHANGED:   Deep link should not be shown for members without location.
CHANGED:   phpDocumentor now sets the memory limit to -1 to prevent issues due to a limited memory usage.
CHANGED:   Bumped maximum recursion setting from 5.000 to 10.000 since errors were still reported.
REMOVED:   `/bin/parse.php` binary; its function is carried out by `phpdoc project:parse`.
REMOVED:   `/bin/transform.php` binary; its function is carried out by `phpdoc project:transform`.
```

2013/06/21: Version 2.0.0b5
---------------------------

```
FIXED:     Root namespace was named `global` in overviews
FIXED:     An empty `Global ('\')` entry pointed to a non-existing default.html in the index
FIXED:     Since tag now shows the version number
FIXED:     Fatal error when an interface's parent could not be resolved in this project
FIXED:     API Documentation menu remains empty
FIXED:     Interface parents now link to the rest of the documentation
FIXED:     Inheritance of methods, constants and properties was not correctly recognized; this is now fixed
FIXED:     When a method argument has a typehint but no @param tag then the typehint will be shown
FIXED      Fatal error in XSL based templates when an interface extends another
```

2013/06/16: Version 2.0.0b4
---------------------------

```
FIXED      Fatal error that occurs when a constant has an error
FIXED      Fatal error that occurs in certain cases with the getError() method
FIXED      Refactored Builder into Assemblers to reduce technical debt
CHANGED    Refactored ProjectDescriptor Builder to separate assembling from filtering and validating
CHANGED    Introduced Symfony Validator component for element validation
CHANGED    Introduced ZF2 Filter component to filter elements (for example with @internal)
```

2013/06/14: Version 2.0.0b3
---------------------------

```
FIXED      Using an @uses tag results in a fatal error during execution
FIXED      Errors are now shown on the errors report of the responsive-twig template
FIXED      The error count on the index page of the responsive-twig template is restored
FIXED      Checkstyle output now functions as expected
FIXED      new-black template failed due to a capitalization issue with the Sourcecode writer
FIXED      Updated all templates to generate a structure.xml
FIXED      Fatal error when a property, constant or method collection contains a null value
FIXED      Fixed several errors in the Old Ocean template
FIXED      Removed broken logging call from Xsl writer
FIXED      Several PHP notices
```

2013/06/09: Version 2.0.0b2
---------------------------

```
FIXED      Package could not be set for constants, resulting in fatal error
FIXED      Default template was still responsive and not responsive-twig
```

2013/06/09: Version 2.0.0b1
---------------------------

The 13th alpha of phpDocumentor contains the final functionality for version 2.0.
A lot of effort has been put in making sure that the API, Object structure and plugin mechanism will remain
backwards compatible until version 3.0.

Features have been changed, removed and added; making this an incredibly large backwards compatibility break compared
to 2.0.0a13.

> Important: if you have written your own XSL based template; please change the template.xml to generate the XML output
> first by adding a transformation with the XmlAst writer. Please look at our existing templates for an example.

> Important: please open an issue for every broken piece of functionality. We have taken the utmost care not to
> introduce bugs but due to large change some might occur.

In the following list we have tried to exhaustively document the list of changes and their impact. Due to the size of
the refactoring it is nigh impossible to make a 100% accurate list. Please contact us if you have questions; the website
https://www.phpdoc.org describes various methods.

```
ADDED      Twig Writer
ADDED      Xml Writer
ADDED      Router component to provide locations for various Url Schemes
ADDED      Responsive-twig template
ADDED      More tests
ADDED      Statistics are being logged to a log file (more will be logged there in the future)
ADDED      Compiler component that adds the ability to inject compiler passes at various points
ADDED      Linker to create object links between the various elements
ADDED      Indexes containing pre-generated lists to make the generation of templates faster
ADDED      More unit tests
FIXED      Define transformations in phpdoc.xml
FIXED      Define multiple templates in phpdoc.xml
FIXED      Performance is improved by re-approaching inheritance from a different angle
FIXED      Various small and unnamed bugs
CHANGED    Moved Inheritance from a Behaviour into the Descriptors
CHANGED    Temporarily removed deprecated report
CHANGED    Target option of parser now only accepts a folder
CHANGED    Various performance improvements
CHANGED    Doctrine Support is moved to its own Service Provider
CHANGED    Rewired dependencies and injection scheme to make better of the DIC
CHANGED    Object graph is written to various cache folders in a directory named 'build' by default
CHANGED    Cache is generated by Zend\Cache
CHANGED    Commands have been moved to their respective component
CHANGED    The Parser has been promoted to Service Provider
CHANGED    The Transformer has been promoted to Service Provider
CHANGED    Parse command has been prepared for internationalization
CHANGED    Template configuration is parsed using JmsSerializer
CHANGED    All logging is now PSR-3 compliant
CHANGED    Removed documentation for components that are still in flux
CHANGED    Updated documentation for existing functionality
DEPRECATED Behaviours are only executed for the XmlAst writer
BC-BREAK   Moved Validators to the Descriptor Builder and refactored for internationalization
BC-BREAK   Parser generates an object graph and not XML (structure.xml, use XmlAst writer now for XML output)
BC-BREAK   Replaced plugin system with Service Providers
```
