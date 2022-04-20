import React from 'react'
import { Wrapper } from './styles'
import { RichText } from 'prismic-reactjs'
import CeneteredContainer from 'Components/containers/CenteredContainer'

const IconList = (props: any) => {
  const { data } = props
  return (
    <CeneteredContainer>
      <h2>{data.primary.title?.[0]?.text}</h2>
      {data.primary.lead_paragraph.length !== 0
        ? (
          <p>{data.primary.lead_paragraph?.[0]?.text}</p>
        )
        : null}
      <Wrapper
        columns={data.primary.columns}
        align={data.primary.content_align}
      >
        {data.items.map((item) => {
          return (
            <article key={item.icon.url}>
              <section>
                <img
                  className={item.image_type}
                  loading="lazy"
                  src={item.icon.url}
                  alt={item.icon.alt}
                  {...item?.icon?.dimensions}
                />
              </section>
              <section>
                {RichText.render(item?.title)}
                {RichText.render(item?.detail)}
              </section>
            </article>
          )
        })}
      </Wrapper>
    </CeneteredContainer>
  )
}

export default IconList
