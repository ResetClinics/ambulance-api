import { StyleSheet } from 'react-native'
import { COLORS, FONTS } from '../../../../constants'

export const styles = StyleSheet.create({
  wrap: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 16
  },
  title: {
    ...FONTS.smallTitle
  },
  value: {
    ...FONTS.smallTitle,
    color: COLORS.primary,
    marginLeft: 5
  },
  container: {
    flex: 1,
  },
  map: {
    width: '100%',
    height: '100%',
    borderRadius: 4,
    overflow: 'hidden'
  },
  btn: {
    marginTop: 16
  }
})
