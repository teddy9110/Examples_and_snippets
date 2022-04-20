import React, { useEffect, useState } from 'react'
import { MastBase } from './styles'

const Mast = (props: any) => {
  const { children } = props
  const [active, setActive] = useState(false)

  useEffect(() => {
    const listener = (e) => {
      if (active && window.scrollY > 0) {
        return
      }
      setActive(window.scrollY > 0)
    }

    window.addEventListener('scroll', listener)
    return () => window.removeEventListener('scroll', listener)
  }, [active])

  return (
    <MastBase {...props} className={`${active ? 'active' : ''}`}>
      {children}
    </MastBase>
  )
}

export default Mast
