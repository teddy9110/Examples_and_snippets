import CustomPageText from 'Helpers/CustomPageData/CustomPage'
import { useMemo } from 'react'
export const useCustomPageData = (props: any) => {
  const {
    slice_type: sliceType,
    primary,
  } = props
  return useMemo(() => {
    const propsTarget = `${sliceType}_selection`
    let sliceTitle = null

    switch (sliceType) {
      case 'activity_level':
        sliceTitle = 'Your Activity Level'
        break
      case 'lifestyle':
        sliceTitle = 'Your Lifestyle'
        break
      case 'dietary_requirements':
        sliceTitle = 'Your Diet'
        break
    }

    const sliceEmoji = `/images/${sliceType}.png`
    const sliceImage = `/images/${sliceType}_image.png`
    let propsKey = 0

    const target = Object.entries(primary).map(
      propsPrimary => {
        if (propsPrimary.includes(propsTarget) && propsTarget !== 'component_selection') {
          propsKey = propsPrimary.indexOf(propsTarget)
        }
        return propsPrimary[propsKey + 1]
      }
    )

    const entries = Object.entries(CustomPageText.CustomContent).map(
      customContent => {
        if (Object.values(target)[0] === customContent[0]) {
          return customContent[1]
        }
        return null
      }
    )

    const imageOnTheLeft = target.find(element => typeof (element) === 'boolean')

    return {
      sliceTitle,
      sliceEmoji,
      sliceImage,
      imageOnTheLeft,
      entries,
    }
  }, [
    sliceType,
    primary,
  ])
}
