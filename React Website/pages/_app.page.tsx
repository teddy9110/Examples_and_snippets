import React, { useEffect } from 'react'
import { ThemeProvider } from 'styled-components'
import Layout from 'Components/Layout'
import PrismicImageProvider from 'Components/image/PrismicImageProvider'
import * as theme from 'StyleGuide/theme'
import GlobalStyle from 'StyleGuide/globalStyle'
import cartStore from 'Store/cartStore'
import CookieNotice from 'Components/CookieNotice'

// global styled need to be in the _app.js file
import 'swiper/swiper.scss'
import 'swiper/components/navigation/navigation.scss'
import 'swiper/components/pagination/pagination.scss'
import Head from 'next/head'

const AppProvider = ({ children }: any) => {
  return <>{children}</>
}

const MyApp = ({ Component, pageProps }: any) => {
  const { loadCart, items } = cartStore((state) => ({
    loading: state.loading,
    items: state.items,
    loadCart: state.loadCart,
  }))

  useEffect(() => {
    loadCart().catch((e) => console.log(e))
  }, [])

  return (
    <ThemeProvider theme={theme}>
      <AppProvider>
        <PrismicImageProvider debounce={500}>
          <GlobalStyle />
          <CookieNotice />
          <Head>
            <meta
              name="viewport"
              content="width=device-width, initial-scale=1.0"
            />
          </Head>
          <Layout mastStyle={Component?.mastStyle} items={items}>
            <Component {...pageProps} />
          </Layout>
        </PrismicImageProvider>
      </AppProvider>
    </ThemeProvider>
  )
}

export default MyApp
