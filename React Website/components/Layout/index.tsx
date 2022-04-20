import React from 'react'
import Mast from 'Components/Mast'
import ResponsiveMenu from 'Components/ResponsiveMenu'
import PrimaryNav from 'Components/PrimaryNav'
import Footer from 'Components/MainFooter'
import Button from 'Components/primitives/form/Button'

export const socials = [
  {
    alias: 'facebook',
    url: 'https://www.facebook.com/teamrhfitness',
    order: 0,
  },
  { alias: 'twitter', url: 'https://twitter.com/teamrhfitness', order: 1 },
  {
    alias: 'youtube-play',
    url: 'https://www.youtube.com/channel/UCNvnh5WF6ggwog5ffOslIUg',
    order: 2,
  },
  {
    alias: 'instagram',
    url: 'https://www.instagram.com/teamrhfitness',
    order: 3,
  },
]

interface LayoutItem {
  quantity?: number
}

interface Props {
  children?: React.ReactNode
  mastStyle?: any
  items?: LayoutItem[]
}

const Layout = ({ children, mastStyle, items = [] }: Props) => {
  const itemAmount = items.reduce((a, b) => a + (b.quantity ?? 0), 0)
  return (
    <>
      <Mast variant={mastStyle}>
        <ResponsiveMenu>
          <PrimaryNav />
        </ResponsiveMenu>
        <a href="/" className="logo">
          <img src="/images/full_logo.svg" alt="Team RH" width="170px" />
        </a>
        <a href="/" className="mobile-logo">
          <img
            src="/images/small_logo.svg"
            className="light"
            height="32px"
            alt="Team RH"
            width="60px"
          />
          <img
            src="/images/small_logo_dark.svg"
            className="dark"
            height="32px"
            alt="Team RH"
            width="60px"
          />
        </a>
        <div className="ctas">
          <Button href={'/pricing'}>Sign Up</Button>
          <a href="/store/cart" className="cart">
            {itemAmount === 0 ? null : <span className="count">{itemAmount}</span>}
            <i className="fa fa-shopping-cart" aria-hidden="true"></i>
          </a>
        </div>
      </Mast>
      {children}

      <Footer as="footer">
        <div style={{ display: 'flex' }}>
          <section>
            <h4>Shop</h4>
            <ul>
              <li>
                <a href="/store/collections/life-plan"> Life Plan</a>
              </li>
              <li>
                <a href="/store/collections/protein">Protein</a>
              </li>
              <li>
                <a href="/store/collections/fitness-collection">Equipment</a>
              </li>
              <li>
                <a href="/store/collections/clothing">Clothing</a>
              </li>
              <li>
                <a href="/store/collections/accessories">Accessories</a>
              </li>
            </ul>
          </section>
          <section>
            <h4>About</h4>
            <ul>
              <li>
                <a href="/about">about</a>
              </li>
              <li>
                <a href="/careers">Careers</a>
              </li>
              <li>
                <a href="/press">Press</a>
              </li>
              <li>
                <a href="/blog">Blog</a>
              </li>
              <li>
                <a href="/pricing">pricing</a>
              </li>
            </ul>
          </section>
          <section>
            <h4>Help</h4>

            <ul>
              <li>
                <a href="/contact-us">Contact us</a>
              </li>
              <li>
                <a href="/cancellation-and-returns">return policy</a>
              </li>
              <li>
                <a href="/delivery-information">Delivery</a>
              </li>
            </ul>
          </section>
          <section className="social-container">
            <h4>Follow us</h4>
            <ul className="social">
              {socials.map(({ alias, url }) => (
                <li key={alias}>
                  <a href={url}>
                    <i className={`fa fa-${alias}`} aria-hidden="true"></i>
                  </a>
                </li>
              ))}
            </ul>
          </section>
        </div>

        <div className="newsletter">
          <iframe
            title="newsletter"
            height="305"
            src="https://f63d4ddb.sibforms.com/serve/MUIEABBKIBS4HAV4gDEMUMQiCOotMyyMLXbHcDqgEEnjeX7Vj0onfYMbRFrIgPL_C42672ysrw9f_xv6UmK6lbfsrBNdQ7VWIeDa5swIcpLONhDHboeklME8EoA2CnTFa0M5QPcLd6R4dg-lKnaOzQZgsbvIlBIATCoseIf1m-HpiUCUPqct8RO8lKanL4eU9QeNyjdJ4c34U5Dr"
            frameBorder="0"
            scrolling="no"
            style={{
              margin: '-30px auto 0 auto',
              width: '100%',
              maxWidth: '540px',
            }}
            allow="fullscreen"
          ></iframe>
        </div>
        <div className="credits">
          <p>&copy; 2021 Team RH Fitness</p>
          <a href="/privacy-policy">Privacy Policy</a>
          <a href="/cookies-policy">Cookie Policy</a>
          <a href="/terms">Terms of service</a>
        </div>
      </Footer>
    </>
  )
}

export default Layout
