import React, { Fragment, useMemo } from 'react'

import {
  ComparisonTable, HeaderTitleCell,
} from './styles'

const TABLE_ITEMS = [
  {
    key: 'trust_pilot',
    name: 'Trustpilot',
  },
  {
    key: 'personalised_fat_loss_plan',
    name: 'Personalised Fat Loss Plan',
  },
  {
    key: 'recipe_database',
    name: 'Recipe Database',
  },
  {
    key: 'no_foods_banned',
    name: 'No Foods Banned',
  },
  {
    key: 'exercise_program',
    name: 'Exercise Program',
  },
  {
    key: 'one_to_one_personal_coaching',
    name: '1:1 Personal Coaching',
  },
  {
    key: 'member_support_group',
    name: 'Support Group',
  },
]

interface Props {
  data: any
}

const CheckMark = ({ value }: { value: boolean }) => (
  <i className={`fa fa-${value ? 'check-circle' : 'times-circle'}`}
    aria-hidden="true"
    style={{ color: `${value ? '#15c78c' : 'red'}` }}
  />
)

const ComparisonsDesktop = ({ data }: Props) => {
  const results = useMemo(() => {
    return [
      ...data.results.filter(({ uid }) => uid === 'trh'),
      ...data.results.filter(({ uid }) => uid !== 'trh'),
    ]
  }, [data])

  return (
    <ComparisonTable>
      <div className="borderHighlight"></div>
      <thead>

      </thead>
      <tbody>
        <tr>
          <HeaderTitleCell />

          {results.map((item) => (
            <td key={item.uid} className={item.uid}>
              <div className="details">
                <img
                  src={item.data.logo.url}
                  alt={item.data.logo.alt}
                  loading="lazy"
                />
                <span className="price">£{Number(item.data.price).toFixed(2)}</span>
                <span className="period">per month</span>
              </div>
            </td>
          ))}
        </tr>

        {TABLE_ITEMS.map(({ key, name }) => (
          <tr key={key}>
            <td className="align-left">
              {key === 'trust_pilot' && (
                <img
                  src={'images/trustpilot_brand.png'}
                  alt="trust pilot logo"
                  style={{ height: '70px' }}
                />
              )}
              {key !== 'trust_pilot' && name}
            </td>
            {results.map((item, index) => (
              <Fragment key={item.uid}>
                {key === 'trust_pilot' && (
                  <td>
                    <img
                      style={{ width: '50%', height: 'auto' }}
                      src={`images/trust-pilot/${item.data.trust_pilot?.[0]?.text.replace('.', '_')}.png`}
                      alt={`trust_pilot_${item.data.trust_pilot?.[0]?.text.replace('.', '_')}_stars`}
                    />
                  </td>
                )}
                {key !== 'trust_pilot' && (
                  <td key={`${key}_is_checked_${index}`}>
                    <CheckMark
                      value={item.data[key]}
                    />
                  </td>
                )}
              </Fragment>
            ))}
          </tr>
        ))}
      </tbody>
    </ComparisonTable>
  )
}

export default ComparisonsDesktop
