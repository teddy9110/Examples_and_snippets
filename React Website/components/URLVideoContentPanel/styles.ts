import ContentContainer from 'Components/containers/ContentContainer'
import styled from 'styled-components'

export const VideoContainer = styled(ContentContainer)`
  .desktop-only {
    display: none;
  }

  .mobile-only {
    display: block;
  }

  .content {
    order: 1;
  }

  .video {
    order: 0;
  }

  &.reverse section:first-child {
    order: 1;
  }

  @media (min-width: 901px) {
    .desktop-only {
      display: block;
    }

    .mobile-only {
      display: none;
    }

    .content {
      order: 1;
    }

    .video {
      order: 0;
    }
  }
`
