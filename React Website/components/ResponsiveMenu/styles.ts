import styled from 'styled-components'

export const ResponsiveMenuBase = styled.div`
  .close,
  .menu {
    all: unset;
    display: block;
    color: ${({ theme }) => theme.colors.secondary};
  }

  > .menu-container {
    overflow-y: auto;
    background: ${({ theme }) => theme.colors.primary_bg};
    position: fixed;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1.4rem;
    text-align: left;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;

    ul {
      margin: 0;
      padding: 0;
    }

    header {
      i {
        color: ${({ theme }) => theme.colors.secondary};
      }

      display: flex;
      justify-content: flex-start;
      width: 100%;
    }

    .button {
      text-align: center;
      width: 100%;
      max-width: 100%;
      margin-top: 2rem;
      display: flex;
    }

    a {
      text-decoration: none;
      font-size: 1.3rem;
      white-space: pre;
      padding: 0.8rem 0;
      display: inline-block;
      color: ${({ theme }) => theme.colors.secondary};
      width: 100%;
      border-bottom: 1px solid #c6c6c8;

      &.active {
        border-bottom: 2px solid ${({ theme }) => theme.colors.primary};
      }
    }

    li {
      width: 100%;
    }

    ul {
      display: flex;
      list-style: none;
      flex-direction: column;
      align-items: start;
    }

    &[data-menu-state="false"] {
      display: none;
    }
  }

  @media (min-width: 804px) {
    padding: 0 1rem;
    z-index: 1;

    .mobile-only {
      display: none;
    }

    .close,
    .menu {
      display: none;
    }

    > .menu-container {
      padding: 0;
      display: flex;
      flex-direction: row;

      a {
        font-size: 1rem;
        border-bottom: 0;
        font-weight: 500;
        padding: 1.9rem 0;
      }

      .button {
        height: 42px;
        margin-top: 0;
        width: max-content;
        max-width: 330px;
        display: flex;
        padding: 0 1.5rem;
      }

      ul {
        flex-direction: row;
        align-items: center;
      }

      li {
        margin: 0;
        padding: 0;
        margin: 0 0.8rem;
        border-bottom: 3px solid transparent;

        &.active {
          border-bottom: 3px solid ${({ theme }) => theme.colors.primary};
        }
      }

      align-items: center;
      background: transparent;
      position: relative;
      height: auto;
      width: auto;
      &[data-menu-state="false"],
      &[data-menu-state="true"] {
        display: block;
      }
    }
  }
`
