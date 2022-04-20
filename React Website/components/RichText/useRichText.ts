import { useMemo } from 'react'

export const useRichText = (content: string) => useMemo(() => content, [content])
