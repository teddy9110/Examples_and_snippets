import React from 'react'
import { CareerContainer, CareerItem } from './styles'

const Career = (props: any) => {
  const { data, block_data: blockData } = props

  return (
    <>
      <CareerContainer>
        <section id="open-roles">
          <h2>{blockData.primary.title?.[0]?.text || 'Open Roles'}</h2>
          <p>{blockData.primary.lead_paragraph?.[0]?.text || ''}</p>
        </section>
        <section className="list">
          {data.results
            ? data.results.map((item, index) => {
              return (
                <CareerItem
                  key={item.data?.role_link.url}
                  href={item.data?.role_link.url}
                  target="_blank"
                  id={`Career-${index}`}
                  className="item"
                >
                  <section>
                    <h3 className="role">{item.data?.role?.[0].text}</h3>
                    <span className="location">
                      {item.data?.location?.[0].text}
                    </span>
                  </section>
                  <i className="fa fa-chevron-right" aria-hidden="true"></i>
                </CareerItem>
              )
            })
            : 'No blog posts.'}
        </section>
      </CareerContainer>
    </>
  )
}

export default Career
