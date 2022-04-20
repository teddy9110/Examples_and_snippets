import React, { useState } from 'react'
import { RichText } from 'prismic-reactjs'
import { Wrapper, Details } from './styles'

const AcordionPanel = (props: any) => {
  const { data } = props
  const [active, setActive] = useState(false)

  const toggle = (index) => {
    if (active === index) return setActive(false)
    setActive(index)
  }

  return (
    <>
      <Wrapper>
        <section className="lead">
          {RichText.render(data?.primary?.title)}
          <div>{RichText.render(data?.primary?.lead_copy)}</div>
        </section>

        {data.items
          ? data.items.map((item, index) => {
            return (
              <Details
                key={item.title[0].text}
                data-index={index}
                className={active === index ? 'active' : undefined}
              >
                <header onClick={() => toggle(index)}>
                  <img
                    src={item.icon.url}
                    alt={item.icon.alt}
                    loading="lazy"
                  />
                  {RichText.render(item.title)}
                  <div className="controls">
                    {active === index
                      ? (
                        <i className="fa fa-chevron-up" aria-hidden="true"></i>
                      )
                      : (
                        <i
                          className="fa fa-chevron-down"
                          aria-hidden="true"
                        ></i>
                      )}
                  </div>
                </header>
                <div>
                  <div>{RichText.render(item.copy)}</div>
                </div>
              </Details>
            )
          })
          : null}
      </Wrapper>
    </>
  )
}

export default AcordionPanel
