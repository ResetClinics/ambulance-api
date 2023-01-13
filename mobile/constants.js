import { Dimensions } from 'react-native'

const { width, height } = Dimensions.get('window')

export const COLORS = {
  primary: '#04607A',
  secondary: '#9ED4E4', // light blue
  blue: '#4794AA',
  overlay: '#4794aa80',
  white: '#fff',
  black: '#000000',
  gray: '#5C5C5C',
  light: '#f1f1f199',
  lightGray: '#0000001f',
  darkGrey: '#CAC4D0',
  transparent: 'transparent',
  error: '#F04438'
}
export const SIZES = {
  // global sizes
  base: 11,
  fs12: 12,
  fs14: 14,
  fs16: 16,
  fs18: 18,
  radius: 12,
  padding: 24,

  // font sizes
  h1: 30,
  h2: 22,
  h3: 16,
  h4: 14,
  body1: 30,
  body2: 22,
  body3: 16,
  body4: 14,
  body5: 12,

  // app dimensions
  width,
  height
}
export const FONTS = {
  title: {
    fontFamily: 'Roboto-Medium', fontSize: SIZES.fs16, lineHeight: 20, color: COLORS.black, letterSpacing: 0.4
  },
  text: {
    fontFamily: 'Roboto-Regular', fontSize: SIZES.fs16, lineHeight: 18, color: COLORS.black, letterSpacing: 0.4
  },
  smallTitle: {
    fontFamily: 'Roboto-Medium', fontSize: SIZES.fs18, lineHeight: 20, color: COLORS.black, letterSpacing: 0.4
  },
  smallText: {
    fontFamily: 'Roboto-Regular', fontSize: SIZES.fs12, lineHeight: 16, color: COLORS.gray, letterSpacing: 0.4
  },
  span: {
    fontFamily: 'Roboto-Medium', fontSize: SIZES.fs14, lineHeight: 20, color: COLORS.primary, letterSpacing: 0.1
  },
  small: {
    fontFamily: 'RedRing-Regular', fontSize: SIZES.base, lineHeight: 12, color: COLORS.gray,
  },
  h1: { fontFamily: 'Roboto-Black', fontSize: SIZES.h1, lineHeight: 36 },
  h2: { fontFamily: 'Roboto-Bold', fontSize: SIZES.h2, lineHeight: 30 },
  h3: { fontFamily: 'Roboto-Bold', fontSize: SIZES.h3, lineHeight: 22 },
  h4: { fontFamily: 'Roboto-Bold', fontSize: SIZES.h4, lineHeight: 22 },
  body1: { fontFamily: 'Roboto-Regular', fontSize: SIZES.body1, lineHeight: 36 },
  body2: { fontFamily: 'Roboto-Regular', fontSize: SIZES.body2, lineHeight: 30 },
  body3: { fontFamily: 'Roboto-Regular', fontSize: SIZES.body3, lineHeight: 22 },
  body4: { fontFamily: 'Roboto-Regular', fontSize: SIZES.body4, lineHeight: 22 },
  body5: { fontFamily: 'Roboto-Regular', fontSize: SIZES.body5, lineHeight: 22 },
}

const appTheme = { COLORS, SIZES, FONTS }

export default appTheme
