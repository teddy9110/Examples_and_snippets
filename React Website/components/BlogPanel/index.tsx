import React, { useEffect, useState } from 'react'
import BlogCard from 'Components/BlogCard'
import SwiperCore, { Pagination, A11y, Autoplay } from 'swiper'
import { RichText } from 'prismic-reactjs'
import { Swiper, SwiperSlide } from 'swiper/react'
import { Wrapper } from './styles'

const BlogPanel = (props: any) => {
  const { data, block_data: blockData } = props
  const [slideNumber, setSlideNumber] = useState(1)

  useEffect(() => {
    const listener = () => {
      if (window.innerWidth > 900) {
        setSlideNumber(3)
      } else {
        setSlideNumber(1)
      }
    }

    listener()
    window.addEventListener('resize', listener)
    return () => window.removeEventListener('resize', listener)
  }, [])

  SwiperCore.use([Autoplay, Pagination, A11y])
  return (
    <>
      <Wrapper style={{ textAlign: 'center', padding: '' }}>
        <section style={{ marginBottom: '2rem' }}>
          {RichText.render(blockData?.primary?.title)}
          {RichText.render(blockData?.primary?.lead_paragraph)}
          <a className="text-cta" href="/blog">
            View All Articles{' '}
            <i className="fa fa-chevron-right" aria-hidden="true"></i>
          </a>
        </section>
        <Swiper
          spaceBetween={50}
          slidesPerView={slideNumber}
          pagination={{ clickable: true }}
        >
          {data.results
            ? data.results.slice(0, 3).map((item, index) => {
              return (
                <SwiperSlide key={item.data.featured_image.url}>
                  <BlogCard
                    as="li"
                    id={`blog-${index}`}
                    style={{ marginBottom: '3rem' }}
                  >
                    <a href={`/blog/${item.uid}`}>
                      <img
                        loading="lazy"
                        src={item.data.featured_image.url}
                        alt={item.data.featured_image.alt}
                        width="377px"
                        height="223px"
                      />
                      <section>
                        <span className="category">
                          {item.data.category?.slug}
                        </span>
                        <h3>
                          {item.data.title[0].text.substring(0, 46)}
                          {item.data.title[0].text.length > 46 ? '...' : null}
                        </h3>
                        <p>{item.data.body[0].text?.substring(0, 200)}</p>
                      </section>
                    </a>
                  </BlogCard>
                </SwiperSlide>
              )
            })
            : 'no blog posts'}
        </Swiper>
      </Wrapper>
    </>
  )
}

export default BlogPanel
