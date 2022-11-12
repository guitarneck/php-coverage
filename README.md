[//]: # (php-coverage v1.0.0)
[![Version 1.0.0][version-badge]][changelog] ![GitHub release (latest by date)][github-release-url] ![GitHub last commit][github-last-commit]
![Packagist Version][packagist-version-url] ![Packagist PHP Version Support][packagist-version-support-url] ![Packagist Downloads][packagist-downloads-url]
![GitHub license][github-license-url] [![Keep a Changelog v1.0.0][changelog-badge]][changelog]


[//]: # (TODO: [![Build Status][travis-image]][travis-url] [![Coverage Status][coveralls-image]][coveralls-url])

---

# PHP Coverage

A [PHP][php-url] framework to retrieve code coverage and get reports of the result in various format.

Can be used with this coverage handlers :
- [XDebug][xdebug-url]
- [PHPDBG][phpdbg-url]
- [PCOV][pcov-url]

And exports this formats :
- clover
- coverage
- coveralls
- dot _(xdebug only)_
- dump _(xdebug only)_
- export _(PHP exported)_
- json _(PHP stringified)_
- lcov
- raw
- serialize _(PHP serialized)_

---

# Table of Contents

* [Install](#install)
* [Command](#command)
    * [Options](#options)
    * [Configuration](#configuration)
* [Using .dot](#using-dot)
* [License](#license)

---

## Install

```shell
composer require guitarneck/php-coverage --dev
```

## Command

- Using php :
```shell
$ php bin/coverage <file> [args...]
```

- Using phpdbg :
```shell
$ phpdbg -qrr bin/coverage <file> [args...]
```

### Options

```text
   --debug                                Show debug informations.

   --excludes=,--excludes,-x              The paths to exclude. Separated by ','.
                                          Ex: vendor/,tests/,inc/lib/

   --format=,--format,-f                  The file format to be generated.
                                          [clover|coverage|coveralls|dot|dump|export|json|lcov|raw|serialize]
                                          dft: coverage

   --handler=,--handler                   The handler to use for coverage.
                                          [xdebug|phpdbg|pcov]
                                          dft: xdebug

   --help,-h                              This help page

   --includes=,--includes,-i              The paths to include. Separated by ','.
                                          Ex: src/,inc/

   --no-extra-filter                      Do not apply extra filtering (includes & excludes).

   --output-path=,--output-path,-p        The paths to output. Separated by ','.
                                          Ex: {DIR},..,reports
                                          - {DIR}: __DIR__ ('coverage/sources')
                                          - ..   : parent path

   --self-coverage                        Only for test purpose of coverage itself.
```

### Configuration

Default configuration can be sets in _sources/Coverage.json_

## Using .dot

[Grahpviz](https://graphviz.org/) is open source graph visualization software.

```shell
$ dot -Tsvg reports\\Hello.dot > Hello.svg
```
---

# License

[MIT © guitarneck][license]

[github-license-url]: https://img.shields.io/github/license/guitarneck/php-coverage
[github-release-url]: https://img.shields.io/github/v/release/guitarneck/php-coverage
[github-last-commit]: https://img.shields.io/github/last-commit/guitarneck/php-coverage

[license]: ./LICENSE
[license-badge]: https://img.shields.io/badge/license-MIT-blue.svg

[version-badge]: https://img.shields.io/badge/version-1.0.0-blue.svg

[changelog]: ./CHANGELOG.md
[changelog-badge]: https://img.shields.io/badge/changelog-Keep%20a%20Changelog%20v1.0.0-%23000000

[packagist-version-url]: https://img.shields.io/packagist/v/guitarneck/php-coverage
[packagist-downloads-url]: https://img.shields.io/packagist/dt/guitarneck/php-coverage

[php-url]: https://www.php.net/
[xdebug-url]: https://xdebug.org/
[phpdbg-url]: https://github.com/krakjoe/phpdbg
[pcov-url]: https://github.com/krakjoe/pcov

[packagist-url]: https://packagist.org/packages/guitarneck/php-coverage
[packagist-version-support-url]: https://img.shields.io/packagist/php-v/guitarneck/php-coverage/1.0.0

[travis-image]: https://img.shields.io/travis/guitarneck/php-coverage.svg?label=travis-ci
[travis-url]: https://travis-ci.org/guitarneck/php-coverage

[coveralls-image]: https://coveralls.io/repos/github/guitarneck/php-coverage/badge.svg?branch=master
[coveralls-url]: https://coveralls.io/github/guitarneck/php-coverage?branch=master

[pull-request]: https://help.github.com/articles/creating-a-pull-request/
[fork]: https://help.github.com/articles/fork-a-repo/