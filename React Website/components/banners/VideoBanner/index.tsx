import React from 'react'
import HeroBanner from 'Components/banners/HeroBanner'
import Button from 'Components/primitives/form/Button'
import { Heading, List, Wrapper } from './styles'

const Check = () => (
  <Wrapper className="fa fa-check" style={{}} aria-hidden="true"></Wrapper>
)

const VideoBanner = (props: any) => {
  const { data } = props

  return (
    <HeroBanner>
      <video
        controls={false}
        muted={true}
        autoPlay={true}
        src={data.primary.video.url}
        poster="/images/heroposter.png"
        loop
        playsInline
      />
      <section>
        <Heading>
          <span className="desktop-break">
            <span className="mobile-break">Join</span>{' '}
            <span className="mobile-break">Team RH</span>{' '}
          </span>
          and <strong>Lose Weight</strong> for Life.
        </Heading>
        <List>
          <li>
            <Check /> No Pills.
          </li>
          <li>
            <Check /> No Shakes.
          </li>
          <li>
            <Check /> Eat Real Food.{' '}
          </li>
        </List>
        <div className="cta">
          {data.items.map((item) => {
            return (
              <Button
                key={item.call_to_action[0].text}
                href={item.call_to_action[0].text}
                variant={item.type}
              >
                Get Started
              </Button>
            )
          })}
        </div>
      </section>
    </HeroBanner>
  )
}

export default VideoBanner
