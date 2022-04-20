import React, { useState } from 'react'
import { ResponsiveMenuBase } from './styles'

const ResponsiveMenu = (props: any) => {
  const { children } = props
  const [menuState, setMenuState] = useState(false)

  const toggleMenu = () => setMenuState(!menuState)

  return (
    <ResponsiveMenuBase className="r-nav">
      <div className="menu-container" data-menu-state={menuState}>
        <header>
          <button className="close" onClick={toggleMenu}>
            <span className="hide">Close</span>
            <i className="fa fa-times" aria-hidden="true"></i>
          </button>
        </header>
        {children}
        <span />
      </div>
      <button className="menu icon-only" onClick={toggleMenu}>
        <span className="hide">Close</span>
        <i className="fa fa-bars" aria-hidden="true"></i>
      </button>
    </ResponsiveMenuBase>
  )
}

export default ResponsiveMenu
