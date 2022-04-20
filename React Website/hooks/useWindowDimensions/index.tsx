import { DependencyList, useState } from 'react'
import useWindowResizeEffect, { WindowDimensions } from 'Hooks/useWindowResizeEffect'

export const useWindowDimensions = (deps: DependencyList = []) => {
  const [dimensions, setDimensions] = useState <WindowDimensions | null>(null)
  useWindowResizeEffect(dimensions => setDimensions(dimensions), deps)
  return { ...dimensions }
}

export default useWindowDimensions
