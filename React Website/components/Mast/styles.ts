import styled from 'styled-components'

interface MastBaseProps {
  variant?: 'transparent'
}

export const MastBase = styled.div<MastBaseProps>`
  background: #fff;
  transition: background linear 0.3s, box-shadow linear 0.3s;
  padding: 1rem;
  z-index: 2;
  position: fixed;
  box-sizing: border-box;
  top: 0;
  left: 0;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.1);

  a,
  button,
  i {
    color: #fff;
  }

  a {
    font-weight: 500;
  }

  .logo {
    display: none;
  }

  .mobile-logo {
    display: block;
    margin-left: 2rem;
    order: 1;
  }

  .r-nav {
    order: 0;
  }

  .ctas {
    order: 2;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 1rem;
    flex: 11;

    .button {
      margin-right: 1rem;
    }
  }

  .cart {
    order: 2;
    position: relative;

    .count {
      border-radius: 1000px;
      position: absolute;
      top: -8px;
      right: -10px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: ${(p) => p.theme.colors.primary};
      color: #fff;
      height: 20px;
      width: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }
  }

  i {
    font-size: 2rem;
    color: ${(p) => p.theme.colors.secondary};
  }

  .dark {
    display: block;
  }

  .light {
    display: none;
  }

  ${(p) => p.variant === 'transparent'
    ? `
      background: transparent;
      box-shadow: none;

      .cart i,
      .menu i {
        color: #fff;
      }

      .dark {
        display: none;
      }

      .light {
        display: block;
      }

      &.active {
        background: #fff;
        box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.1);

        .dark {
          display: block;
        }

        .light {
          display: none;
        }

        .cart i,
        .menu i {
          color: ${p.theme.colors.secondary} !important;
        }
      }
    `
    : null
}

  @media (min-width: 804px) {
    padding: 0 5%;
    justify-content: start;
    background: ${({ theme }) => theme.colors.primary_bg};

    a {
      color: ${({ theme }) => theme.colors.secondary};
      text-decoration: none;
      font-weight: bold;
    }

    .logo {
      display: block;
      order: 0;
      flex: 3;
    }

    .r-nav {
      order: 1;
    }

    .ctas {
      order: 2;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 1rem;
      flex: inherit;
    }

    .button,
    .cart {
      order: 2;
    }

    .mobile-logo {
      display: none;
    }

    i {
      color: ${({ theme }) => theme.colors.secondary} !important;
    }
  }
`
