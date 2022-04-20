import { DependencyList, useEffect } from 'react'

export interface WindowDimensions {
  innerWidth: number
  innerHeight: number
  outerWidth: number
  outerHeight: number
}

const useWindowResizeEffect = (listener: (dimensions: WindowDimensions) => void, deps?: DependencyList) => {
  const boundListener = () => listener({
    innerWidth: window.innerWidth,
    innerHeight: window.innerHeight,
    outerWidth: window.outerWidth,
    outerHeight: window.outerHeight,
  })

  useEffect(() => {
    window.addEventListener('resize', boundListener)
    boundListener()
    return () => window.removeEventListener('resize', boundListener)
  }, deps)
}

export default useWindowResizeEffect
