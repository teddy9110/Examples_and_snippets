import React, { DetailedHTMLProps, InputHTMLAttributes, useEffect, useRef } from 'react'

type Props = DetailedHTMLProps<InputHTMLAttributes<HTMLInputElement>, HTMLInputElement> & {
  email: string
  confirmEmail: string
}

const ConfirmEmailInput = ({ email, confirmEmail, ...props }: Props) => {
  const inputRef = useRef(null)

  useEffect(() => {
    if (!inputRef || email === confirmEmail) {
      return
    }

    const element = inputRef.current

    element.setCustomValidity("The confirm email doesn't match the email.")
    return () => element.setCustomValidity('')
  }, [inputRef, email, confirmEmail])

  return (
    <input
      {...props}
      ref={inputRef}
    />
  )
}

export default ConfirmEmailInput
