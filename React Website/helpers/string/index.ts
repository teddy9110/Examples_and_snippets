
export const truncate = (val: string, maxLength: number = 160, maxWordLength?: number) => {
  const maxSegmentLength = maxWordLength ?? maxLength
  const result = val
    .split(' ')
    .map(str =>
      str.length > maxSegmentLength
        ? str.substring(0, maxSegmentLength - 3) + '...'
        : str
    ).join(' ')

  return (
    result.length > maxLength
      ? result.substring(0, maxLength - 3) + '...'
      : result
  )
}
