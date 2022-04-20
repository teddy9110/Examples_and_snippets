import { useState, useEffect } from 'react'

export const useSlideCount = () => {
  const [width, setWidth] = useState(0)
  useEffect(() => {
    const handler = (event) => {
      setWidth(event.target.window.innerWidth)
    }
    setWidth(window.innerWidth)
    window.addEventListener('resize', handler)
    return () => window.removeEventListener('resize', handler)
  }, [])

  if (width > 900) {
    return 3
  }

  if (width <= 899 && width > 500) {
    return 2
  }

  return 1
}
