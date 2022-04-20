import React from 'react'
import CenteredContainer from 'Components/containers/CenteredContainer'

const TrustPilot = () => {
  return (
    <>
      <CenteredContainer style={{ paddingBottom: '2rem' }}>
        <h2 style={{ fontSize: '2rem', marginBottom: '2rem' }}>
          Read Thousands Of Reviews
        </h2>

        <div
          style={{ width: '100%' }}
          className="trustpilot-widget"
          data-locale="en-GB"
          data-template-id="53aa8912dec7e10d38f59f36"
          data-businessunit-id="5cf66fe3951de70001a69760"
          data-style-height="140px"
          data-style-width="100%"
          data-theme="light"
          data-stars="5"
          data-review-languages="en"
        >
          <a
            href="https://uk.trustpilot.com/review/teamrhfitness.com"
            target="_blank"
            rel="noopener noreferrer"
          >
            Trustpilot
          </a>
        </div>
      </CenteredContainer>
    </>
  )
}

export default TrustPilot
