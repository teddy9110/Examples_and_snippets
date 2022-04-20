import React from 'react'
import Button from 'Components/primitives/form/Button'
import { PageContainer, List } from './styles'

const options = [
  {
    icon: '/images/coins-solid.svg',
    iconAlt: 'coins',
    title: 'Plans and pricing',
    content: 'Choose a plan that works for you.',
    url: '/pricing',
    link_label: 'View Pricing >',
  },
  {
    icon: '/images/shopping-basket-solid.svg',
    iconAlt: 'shopping basket',
    title: 'Shop Products',
    content: 'Browse our product range. ',
    url: '/shop',
    link_label: 'View Shop >',
  },
  {
    icon: '/images/at-solid.svg',
    title: 'Need Support?',
    iconAlt: 'email',
    content: 'Contact our support team',
    url: '/contact-us',
    link_label: 'Get In Touch >',
  },
]

const CustomError = (statusCode) => {
  return (
    <PageContainer>
      <img className="banner" src="/images/404.png" alt="error 404" />
      <h1>{"Oops! Page can't be found."}</h1>
      <p>Have a look at the address to see if you can spot an error.</p>
      <Button href="/">Go to Homepage</Button>

      <List>
        {options.map((item) => {
          return (
            <li key={item.icon}>
              <img
                height="36px"
                width="36px"
                src={item.icon}
                aria-hidden="true"
                alt={item.iconAlt}
                loading="lazy"
              />
              <h2>{item.title}</h2>
              <p>{item.content}</p>
              <a href={item.url}>{item.link_label}</a>
            </li>
          )
        })}
      </List>
    </PageContainer>
  )
}

export default CustomError
