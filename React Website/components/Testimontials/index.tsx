import React, { useRef } from 'react'
import { RichText } from 'prismic-reactjs'
import { TestimonialContainer, TestimonialCard } from './styles'
import SwiperCore, { Pagination, A11y, Autoplay, Navigation } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import { useSlideCount } from 'Helpers/TestimonialsSlideHelper/TestimonialCount'
import PrismicImage from 'Components/image/PrismicImage'
import 'slick-carousel/slick/slick.css'
import 'slick-carousel/slick/slick-theme.css'

const Testimonials = (props: any) => {
  const { data, block_data: blockData } = props
  const swiperRef = useRef<any>(null)
  const slides = useSlideCount()

  SwiperCore.use([Autoplay, Pagination, A11y, Navigation])

  return (
    <>
      <TestimonialContainer>
        <section>
          <h3>
            {blockData.primary.title?.[0]?.text || 'Member Transformations'}
          </h3>
        </section>
        <section className="cc">
          <Swiper
            loop={true}
            ref={swiperRef}
            spaceBetween={67}
            slidesPerView={slides}
            autoplay={{ delay: 5000 }}
            navigation
            pagination={{ clickable: true }}
            scrollbar={{ draggable: true }}
          >
            {data.results
              ? data.results.slice(0, 4).map((item, index) => {
                return (
                  <SwiperSlide key={item.data.name?.[0]?.text}>
                    <TestimonialCard
                      onMouseEnter={() => {
                        swiperRef.current.swiper.autoplay.stop()
                      }}
                      onTouchStart={() => {
                        swiperRef.current.swiper.autoplay.stop()
                      }}
                      onMouseLeave={() => {
                        swiperRef.current.swiper.autoplay.start()
                      }}
                      onTouchEnd={() => {
                        swiperRef.current.swiper.autoplay.start()
                      }}
                      id={`testimonial-${index}`}
                      className="item"
                      style={{ textAlign: 'center' }}
                    >
                      <section>
                        <PrismicImage
                          url={item.data.member_image?.url}
                          alt={item.data.member_image?.alt || ' '}
                          loading="lazy"
                          width={395}
                          height={395}
                          style={{ borderRadius: '15px' }}
                        />
                      </section>
                      <section className="quote">
                        {RichText.render(item.data.story)}
                        <h3>{item.data.name?.[0]?.text}</h3>
                      </section>
                      <section className="trustpilot">
                        <img
                          src="images/trustpilot_5.png"
                          alt="Trust Pilot Review"
                        />
                      </section>
                    </TestimonialCard>
                  </SwiperSlide>
                )
              })
              : 'no blog posts'}
          </Swiper>
        </section>
      </TestimonialContainer>
    </>
  )
}

export default Testimonials
