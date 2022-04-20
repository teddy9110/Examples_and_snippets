import React from 'react'
import { useRouter } from 'next/router'

export const items = [
  { alias: 'Home', url: '/', order: 0, className: 'mobile-only' },
  // { alias: 'How it works', url: '/how-it-works', order: 0 },
  { alias: 'Pricing', url: '/pricing', order: 1 },
  { alias: 'Transformations', url: '/transformations', order: 2 },
  { alias: 'About', url: '/about', order: 3 },
  { alias: 'Blog', url: '/blog', order: 4 },
  { alias: 'Shop', url: '/store', order: 5 },
  { alias: 'Contact Us', url: '/contact-us', order: 6, className: 'mobile-only' },
]

const navItems = ({ alias, url, order, className = '' }) => {
  const router = useRouter()
  const activeRoute = router.asPath.split('/')[1].toLocaleLowerCase()

  return (
    <li
      key={alias}
      className={`
        ${className} ${
      activeRoute === alias.toLocaleLowerCase() ? 'active' : ''
    }
      `}
      data-order={order}
    >
      <a href={url}>{alias}</a>
    </li>
  )
}

const PrimaryNav = () => <ul>{items.map(navItems)}</ul>

export default PrimaryNav
