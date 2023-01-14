import React from 'react'
import {
  BottomNavigation, Layout, ProfileScreen, ScreenLayout
} from '../../components'

export const Profile = ({ navigation }) => (
  <ScreenLayout>
    <Layout>
      <ProfileScreen />
    </Layout>
    <BottomNavigation navigation={navigation} />
  </ScreenLayout>
)
