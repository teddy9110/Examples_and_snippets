import React from 'react'
import ContentContainer from 'Components/containers/ContentContainer'
import { useCustomPageData } from 'helpers/CustomPageData/index'

const CustomPageBody = (props: any) => {
  const { data } = props

  const customPageData: any = useCustomPageData(data)

  const entryData = customPageData.entries.filter((el) => {
    return el !== null && typeof el !== 'undefined'
  })
  return (
    <>
      {customPageData.dataTarget !== 'component_selection' &&
        <ContentContainer style={{ gridTemplateColumns: 'unset !important' }}>
          <div className={`${customPageData.imageOnTheLeft ? 'right-align' : 'left-align'} content`}>

            {(customPageData.imageOnTheLeft !== null && customPageData.imageOnTheLeft === true) &&
              <>
                <div className='light-background'>
                  <div>
                    <img className="entry-emoji" src={customPageData.sliceEmoji} alt={data.slice_type} />
                    <h5 style={{ display: 'inline-block !important', paddingLeft: '10px' }}> {customPageData.sliceTitle}</h5>
                  </div>
                  {
                    entryData[0].map((item, index) => {
                      return <div key={index}>{item[0]}</div>
                    })
                  }
                </div>
                <div>
                  <img src={customPageData.sliceImage} />
                </div>
              </>
            }

            {(customPageData.imageOnTheLeft !== null && customPageData.imageOnTheLeft === false) &&
              <>
                <div>
                  <img src={customPageData.sliceImage} />
                </div>
                <div className='light-background'>
                  <div>
                    <img className="entry-emoji" src={customPageData.sliceEmoji} alt={data.slice_type} />
                    <h5 style={{ display: 'inline-block !important', paddingLeft: '10px' }}> {customPageData.sliceTitle}</h5>
                  </div>
                  {
                    entryData[0].map((item, index) => {
                      return <div key={index}>{item[0]}</div>
                    })
                  }
                </div>
              </>
            }

          </div>
        </ContentContainer>
      }
    </>
  )
}

export default CustomPageBody
