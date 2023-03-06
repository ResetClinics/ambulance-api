import React from 'react'
import { createNativeStackNavigator } from '@react-navigation/native-stack'
import {
  BottomNavigation, ScreenLayout
} from '../../components'
import { COLORS } from '../../../constants'
import { ProfileScreen } from './ProfileScreen'
import { EditProfileScreen } from './EditProfileScreen'

const Stack = createNativeStackNavigator()

export const Profile = ({ navigation }) => (
  <ScreenLayout>
    <Stack.Navigator>
      <Stack.Screen name="profileScreen" component={ProfileScreen} options={{ headerShown: false }} />
      <Stack.Screen
        name="editProfile"
        component={EditProfileScreen}
        options={{
          title: 'Редактировать Профиль',
          headerBackTitleVisible: false,
          headerTitleAlign: 'center',
          headerTintColor: COLORS.white,
          headerStyle: {
            backgroundColor: COLORS.primary,
          },
        }}
      />
    </Stack.Navigator>
    <BottomNavigation navigation={navigation} />
  </ScreenLayout>
)
