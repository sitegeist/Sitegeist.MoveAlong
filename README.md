# Sitegeist.MoveAlong

> Configure fallback nodes in case of 404

## Summary

This package enables editable 404 pages. It allows you to configure several 404 pages in the page tree, in case you need different content or styling in different situations. 

**ATTENTION: This package will add a http-component via Settings.yaml. This component will try to find a matching 404 page for the current path an override the http-request accordingly.** 

## Installation

For now this package is not listed at packagist.org, so it needs to be configured via the `repositories` option in your composer.json.

```json
{
  "repositories": [
    {
      "url": "ssh://git@git.sitegeist.de:40022/sitegeist/Sitegeist.MoveAlong.git",
      "type": "vcs"
    }
  ]
}
```

You can then require it as a regular dependency:

```json
{
  "dependencies": {
    "sitegeist/movealong": "@dev"
  }
}
```

Currently `@dev` is recommended, since this package is still under development. Later on it should be replaced by the according semver string.

After you finished configuring your composer.json, run the following command to retrieve the package:

```shell
composer update sitegeist/movealong
```

## Usage

### Settings

To activate Sitegeist.MoveAlong, the smallest configuration you're going to need is the following:

```yaml
Sitegeist:
  MoveAlong:
    enable: TRUE
```

By default, Sitegeist.MoveAlong will match any requestPath and map it to `/404`. You can configure your own rules to handle Dimensions for example:

```yaml
Sitegeist:
  MoveAlong:
    rules:
      english:
        pattern: 'en\/.*'
        target: 'en/404'
      german:
        pattern: 'de\/.*'
        target: 'de/404'
```

The rule pattern and targets support pattern matching and replacement:

```yaml
Sitegeist:
  MoveAlong:
    rules:
      main:
        pattern: '^(en|de)\/.*'
        target: '$1/404'
```

!!! Regardles of any existing `defaultUriSuffix` configuration, you need to omit that uri part. So, if your 404 page is reachable via `404.html`, you need to configure `404` as your target.

If you just want to override the default behavior, you can overwrite the pre-configured `all` rule:

```yaml
Sitegeist:
  MoveAlong:
    rules:
      all:
        target: 'NotFound' # will display NotFound.html
```

If you want to change the order in which the rules apply, you can add a `position` argument to your rule configuration:

```yaml
Sitegeist:
  MoveAlong:
    rules:
      english:
        ...
      german:
        position: 'before english'
        ...
```

### TypoScript

Since the fallback mechanism will cause Neos to think, that a node has been found, the system won't respond with a 404 status code anymore. Therefore, some TypoScript configuration is applied, to determine, whether we are on an error page and then send a 404 status code accordingly.

By default, that TypoScript will assume, that your error page will have a `uriPathSegment` property that is set to `404`.

If this is not the case for your configuration, you can simply apply a different rule for that by overriding the `Sitegeist.MoveAlong:Match404Page` prototype:

```typoscript2
prototype(Sitegeist.MoveAlong:Match404Page) {
  condition.@process.isNotFoundDocument = ${value && q(node).property('is404Page') == true}
}
```
