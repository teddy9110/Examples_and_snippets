import React from 'react'
import Link from 'next/link'
import { PageNumber, Pagination, PageChangeLink, PageNumberContainer } from './styles'

interface Props {
  page: number
  pageCount: number
  createPageLink: (pageNumber: number) => string
}

const PageSelector = ({ page, pageCount, createPageLink }: Props) => {
  return (
    <Pagination>
      {page > 1 && (
        <Link href={createPageLink(page - 1)}>
          <PageChangeLink className="fa fa-chevron-left" />
        </Link>
      )}

      {Array.from({ length: pageCount }, (_, index) => {
        return (
          <PageNumberContainer>
            <Link href={createPageLink(index + 1)}>
              <PageNumber className={page === index + 1 ? 'active' : ''}>
                {index + 1}
              </PageNumber>
            </Link>
          </PageNumberContainer>
        )
      })}

      {page < pageCount && (
        <Link href={createPageLink(page + 1)}>
          <PageChangeLink className="fa fa-chevron-right" />
        </Link>
      )}
    </Pagination>
  )
}

export default PageSelector
