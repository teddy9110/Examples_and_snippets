import React, { ComponentProps, CSSProperties } from 'react'
import { Elements, HTMLSerializer } from 'prismic-reactjs'
import PrismicImage from 'Components/image/PrismicImage'

interface Options {
  createImageStyle?: (params: { url: string, width: string, height: string }) => {
    container?: CSSProperties
    image?: ComponentProps<typeof PrismicImage>['style']
  }
}

const createHtmlSerializer = (options: Options = {}): HTMLSerializer<React.ReactNode> => {
  const {
    createImageStyle = (() => ({})) as Options['createImageStyle'],
  } = options

  const HTMLSerializer = (type: string, element: any) => {
    switch (type) {
    // Add a class to paragraph elements
      case Elements.paragraph: {
        if (element.text === '') {
          return <p style={{ height: '11px' }} />
        }

        return null
      }

      // Optimise images
      case 'image': {
        const {
          url,
          alt,
          linkTo,
          dimensions: { width: imageWidth, height: imageHeight },
        } = element

        const { image: imageStyle, container: containerStyle } = createImageStyle({
          url,
          width: imageWidth,
          height: imageHeight,
        })

        const child = (
          <PrismicImage
            url={url}
            alt={alt}
            width={imageWidth}
            height={imageHeight}
            style={imageStyle}
          />
        )

        return (
          <p
            className="block-img"
            style={containerStyle}
          >
            {linkTo && (
              <a href={linkTo.url}>
                {child}
              </a>
            )}
            {!linkTo && child}
          </p>
        )
      }

      default:
        return null
    }
  }

  return HTMLSerializer
}

export default createHtmlSerializer
