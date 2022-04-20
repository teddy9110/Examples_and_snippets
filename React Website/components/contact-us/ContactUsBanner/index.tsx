import React from 'react'
import { Banner, BannerHeader, BannerInner } from './style'

const ContactBanner = () => {
  return (
    <Banner>
      <BannerInner>
        <BannerHeader>
          <h2>Get in  touch!</h2>
          <span>We love hearing from our members and our dedicated customer service team are always happy to help out!</span>
        </BannerHeader>
      </BannerInner>
      <BannerInner>
        <picture>
          <source srcSet="images/contact-us.webp" type="image/webp" />
          <source srcSet="images/contact-us.png" type="image/png" />
          <img src="images/contact-us.png" alt="rh-holding-luna"/>
        </picture>
      </BannerInner>
    </Banner>
  )
}

export default ContactBanner
