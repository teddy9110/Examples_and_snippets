import React from 'react'
import PriceComparison from 'Components/PriceComparison'
import { Wrapper } from './styles'

interface Props {
  handle: string
  title: string
  variants: any[]
  images: any[]
  description: string
}

const SearchItem = (props: Props) => {
  const { variants, images, title, handle, description } = props

  return (
    <Wrapper>
      <a href={`/store/${handle}`}>
        <div className="thumb">
          <img src={images[0].src} alt={images[0].altText} loading="lazy" />
        </div>
        <section>
          <h3>{title}</h3>
          <PriceComparison
            variant={variants[0]}
          />
          <p>{description.substring(0, 200)}</p>
        </section>
      </a>
    </Wrapper>
  )
}

export default SearchItem
