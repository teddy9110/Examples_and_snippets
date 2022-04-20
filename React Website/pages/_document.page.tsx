import React from 'react'
import { ServerStyleSheet } from 'styled-components'
import Document, {
  Html,
  Head,
  Main,
  NextScript,
} from 'next/document'

export default class MyDocument extends Document {
  render () {
    return (
      <Html lang="en">
        <Head />
        <head>
          <title>Team RH</title>
          <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="/images/icons/apple-touch-icon.png"
          />
          <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="/images/icons/favicon-32x32.png"
          />
          <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="/images/icons/favicon-16x16.png"
          />
          <link rel="manifest" href="/site.webmanifest" />
          <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#d81435" />
          <meta name="msapplication-TileColor" content="#d81435" />
          <meta name="theme-color" content="#ffffff"></meta>
          <link rel="preconnect" href="https://fonts.gstatic.com" />
          <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;1,700&display=swap"
            rel="stylesheet"
          />
          <script
            type="text/javascript"
            src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js"
            async
          ></script>
          {/* REFERSION TRACKING: BEGIN */}
          <script
            type="text/javascript"
            src="https://teamrh.refersion.com/tracker/v3/pub_ae0e6b7001c5fbe9047f.js"
            // src="//www.refersion.com/tracker/v3/pub_ae0e6b7001c5fbe9047f.js"
          ></script>
          <script>_refersion();</script>
          {/* REFERSION TRACKING: END */}
          <script async defer src="https://static.cdn.prismic.io/prismic.js?new=true&repo=website-rh"></script>
          {process.env.NEXT_PUBLIC_ENV === 'production'
            ? (
              <>
                <script
                  dangerouslySetInnerHTML={{
                    __html: `(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
              new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
              j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
              'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
              })(window,document,'script','dataLayer','GTM-PV8HG2S');`,
                  }}
                />
                <script
                  dangerouslySetInnerHTML={{
                    __html: `!function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '560880804783162');
                fbq('track', 'PageView');`,
                  }}
                />
              </>
            )
            : null}
        </head>
        <body>
          <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          />
          {process.env.NEXT_PUBLIC_ENV === 'production'
            ? (
              <noscript>
                <iframe
                  title="gtm"
                  src="https://www.googletagmanager.com/ns.html?id=GTM-PV8HG2S"
                  height="0"
                  width="0"
                  style={{ display: 'none', visibility: 'hidden' }}
                ></iframe>
                <img
                  title="fb"
                  height="1"
                  width="1"
                  style={{ display: 'none' }}
                  alt="facebook script"
                  src="https://www.facebook.com/tr?id=560880804783162&ev=PageView&noscript=1"
                />
              </noscript>
            )
            : null}
          <Main />
          <NextScript />
        </body>
      </Html>
    )
  }
}

MyDocument.getInitialProps = async (ctx) => {
  const sheets = new ServerStyleSheet()
  const originalRenderPage = ctx.renderPage

  ctx.renderPage = () =>
    originalRenderPage({
      enhanceApp: (App) => (props) => sheets.collectStyles(<App {...props} />),
    })

  const initialProps = await Document.getInitialProps(ctx)

  return {
    ...initialProps,
    // Styles fragment is rendered after the app and page rendering finish.
    styles: [
      ...React.Children.toArray(initialProps.styles),
      sheets.getStyleElement(),
    ],
  }
}
