import React from 'react'
import SwiperCore, { Pagination, A11y, Autoplay, Navigation } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import { TeamPanelContainer, TeamPanelCard, DesktopPerksContiner } from './styles'
import { RichText } from 'prismic-reactjs'
import IconList from 'Components/lists/IconList'
import { transformData } from './data'

const TeamPanel = (props: any) => {
  const { data, block_data: blockData } = props
  const pageType = data.items != null // true == perks page
  const usableData = transformData(data, pageType)

  SwiperCore.use([Autoplay, Pagination, A11y, Navigation])
  return (
    <>
      <TeamPanelContainer >
        <section
          className={`${pageType ? 'hide-on-desktop' : ''}`}
          style= {{ paddingTop: pageType ? '' : '4rem' }}
        >
          <h2>{blockData?.primary.title?.[0]?.text || data?.primary?.title?.[0]?.text}</h2>
        </section>
        <section className={`cc ${pageType ? 'hide-on-desktop' : ''}`}>
          <Swiper
            id="team"
            navigation
            slidesPerView={1}
            pagination={{ clickable: true }}
          >
            {Object.keys(usableData).map(function (key, index) {
              return (
                <SwiperSlide key={usableData[key].image?.mobile?.url}>
                  <TeamPanelCard
                    id={`TeamPanel-${index}`}
                    style={{
                      background: pageType ? 'unset' : '#1b2337',
                      gridTemplateColumns: pageType ? 'none' : '',
                    }}
                    className="item">
                    <picture>
                      <source
                        srcSet={usableData[key].image?.mobile?.url}
                        media="(max-width: 901px)"
                      />
                      <img
                        className={`${pageType ? 'icon-image' : ''}`}
                        src={usableData[key].image?.url}
                        alt={usableData[key].image?.alt || ' '}
                        loading="lazy"
                      />
                    </picture>
                    <div className='content'>
                      {!pageType && RichText.render(usableData[key].name)}
                      {pageType &&
                        <h3 className='benefits-header' > {pageType && usableData[key].name} </h3>
                      }
                      <div className={` ${pageType ? 'benefits' : ''}`} >
                        {pageType && usableData[key].body}

                        {!pageType && usableData[key].role?.[0].text}
                      </div>
                      {!pageType && RichText.render(usableData[key].body)}
                    </div>
                  </TeamPanelCard>
                </SwiperSlide>
              )
            })
            }
          </Swiper>
        </section>
      </TeamPanelContainer>
      {pageType &&
      <DesktopPerksContiner>
        <section className={`${pageType ? 'hide-on-mobile' : ''}`}>
          <IconList key='hide-on-mobile' className='desktop_perks' data={props.data}/>
        </section>
      </DesktopPerksContiner>
      }
    </>
  )
}

export default TeamPanel
