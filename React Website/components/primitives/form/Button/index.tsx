import React from 'react'
import { Wrapper } from './styles'

interface Props extends React.HTMLProps<HTMLButtonElement> {
  type?: string
  loading?: boolean
  variant?: string
  className?: string
  href?: string
}

const Button = ({
  children,
  disabled,
  loading,
  variant,
  className = 'unnamed',
  ...props
}: Props) => {
  const as = props.href ? 'a' : 'button'

  return (
    <Wrapper
      className={`${className} button`}
      as={as}
      {...props}
      data-variant={variant}
      disabled={disabled ?? loading}
      data-loading={loading}
    >
      {loading
        ? (
          <i className="fa fa-spinner" aria-hidden="true" />
        )
        : (
          children
        )}
    </Wrapper>
  )
}

export default Button
