type DangerousElement <E extends Element = Element> = E & {
  scrollIntoViewIfNeeded?: (center?: boolean) => void
}
