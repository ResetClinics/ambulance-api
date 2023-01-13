import React from 'react'
import {
  BottomNavigation, Layout, ProfileScreen, ScreenLayout
} from '../../components'
import { itemsAdd } from '../../data/itemsAdd'

export const Profile = ({ navigation }) => (
  <ScreenLayout>
    <Layout>
      <ProfileScreen />
    </Layout>
    <BottomNavigation navigation={navigation} items={itemsAdd} />
  </ScreenLayout>
)
