import React, { useEffect, useState } from 'react'
import { Container, Wrapper } from './styles'
import Button from 'Components/primitives/form/Button'

const CookieNotice = () => {
  const [cookieBanner, setCookieBanner] = useState(false)

  const hideBanner = () => {
    setCookieBanner(false)
    localStorage.setItem('hide_cookie_banner', 'true')
  }

  useEffect(() => {
    const cookieBanner = localStorage.getItem('hide_cookie_banner') === 'true'
    if (!cookieBanner) {
      setCookieBanner(true)
    }
  }, [])

  return (
    <Container data-active={cookieBanner}>
      <Wrapper>
        <p>
          {`
            Our website uses cookies to ensure you get the best experience on our
            website - and don't worry, they're zero calories! See our
          `}
          <a href="/privacy-policy"> privacy policy</a> for more info.
        </p>
        <Button onClick={hideBanner}>Got it!</Button>
      </Wrapper>
    </Container>
  )
}

export default CookieNotice
