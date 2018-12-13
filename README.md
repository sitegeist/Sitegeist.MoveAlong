# Sitegeist.MoveAlong

**Render 4xx status pages via fusion**

### Configuration

The status codes that are handled via fusion error-rendering
can be controlled via settings.

```yaml
Neos:
  Flow:
    error:
      exceptionHandler:
        renderingGroups:
          notFoundExceptions:
            matchingStatusCodes: [ 403, 404, 410 ]
```

The fusion code that actually renders the error-message.

```fusion
#
# Main error matcher
#
error = Neos.Fusion:Case {

	#
	# Find the document to render in case of 404
	#
	@context.notFoundDocument = ${q(site).children('[instanceof Neos.Neos:Document]').filter('[uriPathSegment="404"]').get(0)}

	#
	# Custom matcher for 404 status
	#
	4xx {
		condition = ${statusCode >= 400 && statusCode < 500 && notFoundDocument}
		renderer = Neos.Fusion:Renderer {
			@context.node = ${notFoundDocument}
			@context.documentNode = ${notFoundDocument}
			renderPath = '/root'
		}
	}

	#
	# Default rendering of classic error-message
	#
	default {
		position = 'end 9999'
		condition = true
		renderer = Sitegeist.MoveAlong:ErrorMessage {
			exception = ${exception}
			renderingOptions = ${renderingOptions}
			statusCode = ${statusCode}
			statusMessage = ${statusMessage}
			referenceCode = ${referenceCode}
		}
	}
}

```

### Authors & Sponsors

* Wilhelm Behncke - behncke@sitegeist.de
* Martin Ficzel - ficzel@sitegeist.de

*The development and the public-releases of this package is generously sponsored by our employer http://www.sitegeist.de.*

## Installation

Sitegeist.MoveAlong is available via packagist. Just run `composer require sitegeist/movealong`. We use semantic-versioning so every breaking change will increase the major-version number.

## License

see [LICENSE file](LICENSE)
