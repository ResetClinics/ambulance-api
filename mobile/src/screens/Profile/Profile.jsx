import React from 'react'
import { Avatar } from 'react-native-paper'
import {
  StyleSheet, Text, View
} from 'react-native'
import {
  BottomNavigation, Layout, ScreenLayout
} from '../../components'
import { COLORS, FONTS, SIZES } from '../../../constants'

const defaultImg = require('../../../assets/images/default.png')

const img = ''

export const Profile = ({ navigation }) => (
  <ScreenLayout>
    <Layout>
      <View>
        <Avatar.Image size={140} source={img || defaultImg} style={styles.root} />
        <Text style={styles.title}>Иванов Иван Иванович</Text>
        <Text style={styles.text}>Невролог-терапевт</Text>
      </View>
    </Layout>
    <BottomNavigation navigation={navigation} />
  </ScreenLayout>
)

const styles = StyleSheet.create({
  title: {
    textAlign: 'center',
    fontSize: SIZES.fs18,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black,
    marginBottom: 16,
    ...FONTS.h2,
  },
  text: {
    textAlign: 'center',
    fontSize: SIZES.fs16,
    lineHeight: 16,
    letterSpacing: 0.4,
    color: COLORS.black,
  },
  root: {
    marginLeft: 'auto',
    marginRight: 'auto',
    marginBottom: 16,
    backgroundColor: COLORS.transparent
  }
})
