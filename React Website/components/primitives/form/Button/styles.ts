import styled from 'styled-components'
import { lighten, darken } from 'polished'
import { ComponentType, HTMLProps } from 'react'

export const Wrapper: ComponentType<HTMLProps<HTMLButtonElement>> = styled.button`
  all: unset;
  display: flex;
  max-width: 200px;
  height: 42px;
  min-height: 42px;
  justify-content: center;
  align-items: center;
  padding: 0 1.5rem;
  box-sizing: border-box;
  background: ${({ theme }) => theme.colors.primary};
  color: ${({ theme }) => theme.colors.button_text};
  -webkit-text-fill-color: ${({ theme }) => theme.colors.button_text};
  transition: all 0.3s;
  font-size: 1.1rem;
  font-weight: normal !important;
  white-space: nowrap;

  i {
    font-size: 1.2rem;
    margin-right: 0.3rem;
  }

  &:hover {
    cursor: pointer;
    background: ${({ theme }) => darken(0.05, theme.colors.primary)};
  }

  &:disabled {
    background: ${({ theme }) => lighten(0.5, theme.colors.primary)};
    cursor: loader;
  }

  &[data-loading="true"] {
    i {
      animation-name: spin;
      animation-duration: 2000ms;
      animation-iteration-count: infinite;
      animation-timing-function: linear;
    }
  }

  &[data-variant="small"] {
    height: 32px;
    padding: 0 1rem;
    font-size: 0.8rem;
  }

  &[data-variant="icon-only"] {
    background: none;
    color: #555;
    -webkit-text-fill-color: #555;
    height: 32px;
    padding: 0;
    font-size: 0.8rem;

    &:hover {
      color: ${darken(0.15, '#555')};
    }
  }

  &[data-variant="outline"] {
    background: none;
    color: #fff;
    -webkit-text-fill-color: #fff;
    border: 2px solid #fff;

    &:hover {
      background: none;
    }
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }

    to {
      transform: rotate(360deg);
    }
  }
`
