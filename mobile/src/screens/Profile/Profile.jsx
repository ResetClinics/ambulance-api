import React from 'react'
import { BottomNavigation, Layout, ProfileScreen } from '../../components'

export const Profile = ({ navigation }) => (
  <Layout>
    <ProfileScreen />
    <BottomNavigation navigation={navigation} />
  </Layout>
)
