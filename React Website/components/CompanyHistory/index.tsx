import React, { useState } from 'react'
import { Wrapper, HistoryContainer } from './styles'
import { RichText } from 'prismic-reactjs'

const CompanyList = (props: any) => {
  const { data } = props
  const [active, setActive] = useState(0)
  const items = data.results[0].data.history_items
  return (
    <HistoryContainer>
      <header>
        <h2>Our Story</h2>
        <div>
          <a
            href=""
            onClick={(e) => {
              e.preventDefault()
              const index = active - 1

              if (active <= 0) {
                return
              }

              setActive(index)
              const $slide: DangerousElement = document.querySelector(`#history_${index}`)!

              if ($slide.scrollIntoViewIfNeeded != null) {
                $slide.scrollIntoViewIfNeeded(false)
              } else if ($slide.scrollIntoView) {
                $slide.scrollIntoView(false)
              }
            }}
          >
            <i className="fa fa-chevron-left" aria-hidden="true"></i>
          </a>

          <a
            href=""
            onClick={(e) => {
              e.preventDefault()
              const index = active + 1

              if (active >= items.length) return
              setActive(index)
              const $slide: DangerousElement = document.querySelector(`#history_${index}`)!

              if ($slide.scrollIntoViewIfNeeded != null) {
                $slide.scrollIntoViewIfNeeded(false)
              } else if ($slide.scrollIntoView) {
                $slide.scrollIntoView(false)
              }
            }}
          >
            <i className="fa fa-chevron-right" aria-hidden="true"></i>
          </a>
        </div>
      </header>
      {data.primary?.lead_paragraph.length !== 0
        ? (
          <p>{data.primary?.lead_paragraph?.[0]?.text}</p>
        )
        : null}
      <Wrapper id="history">
        {items.map((item, index) => {
          return (
            <>
              <article id={`history_${index}`} key={item?.icon.url}>
                <img src={item?.icon.url} alt={item?.icon.alt} loading="lazy" />
                <span className="dot" />
                {RichText.render(item?.date)}
                {RichText.render(item?.description)}
              </article>
            </>
          )
        })}
      </Wrapper>
    </HistoryContainer>
  )
}

export default CompanyList
