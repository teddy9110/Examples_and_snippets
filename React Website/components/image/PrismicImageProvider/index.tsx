import React, { ComponentProps, createContext, useContext, useEffect, useState } from 'react'
import { WindowDimensions } from 'Hooks/useWindowResizeEffect'
import useWindowDimensions from 'Hooks/useWindowDimensions'

const PrismicImageContext = createContext <WindowDimensions | null>(null)

type Props = Omit<ComponentProps<typeof PrismicImageContext.Provider>, 'value'> & {
  /**
   * How long in ms before a size change triggers a re-render
   */
  debounce?: number
}

const PrismicImageProvider = ({ debounce, ...props }: Props) => {
  const actualDimensions = useWindowDimensions()
  const [dimensions, setDimensions] = useState <WindowDimensions | null>(null)

  useEffect(() => {
    if (dimensions == null) {
      setDimensions(actualDimensions)
    }

    let timeoutRef: NodeJS.Timeout | null = null

    timeoutRef = setTimeout(() => {
      setDimensions(actualDimensions)
      timeoutRef = null
    }, debounce)

    return () => {
      if (timeoutRef != null) {
        clearTimeout(timeoutRef)
      }
    }
  }, [
    actualDimensions?.innerWidth,
    actualDimensions?.innerHeight,
    debounce,
  ])

  return (
    <PrismicImageContext.Provider
      {...props}
      value={dimensions}
    />
  )
}

export const usePrismicImageWindowDimensions = () => useContext(PrismicImageContext)

export default PrismicImageProvider
