import React, { useContext } from 'react'
import { createStackNavigator } from '@react-navigation/stack'
import { NavigationContainer } from '@react-navigation/native'
import {
  PasswordForgetScreen, Sent, SignInScreen,
} from '../../screens'
import { Menu } from '../Menu'
import { AuthContext } from '../../context/AuthContext'
import { Loading } from '../../components'

const RootStack = createStackNavigator()

export const Routes = () => {
  const { isLoading, userToken } = useContext(AuthContext)
  const { logout } = useContext(AuthContext)

  if (isLoading) {
    return (
      <Loading />
    )
  }

  return (
    <NavigationContainer>
      <RootStack.Navigator>
        {userToken !== null ? (
          <RootStack.Screen
            name="App"
            options={{ headerShown: false }}
          >
            {(props) => (
              // eslint-disable-next-line react/jsx-props-no-spreading
              <Menu {...props} handleSignOut={logout} />
            )}
          </RootStack.Screen>
        ) : (
          <>
            <RootStack.Screen
              name="Sign In"
              options={{ headerShown: false }}
            >
              {(props) => (
                // eslint-disable-next-line react/jsx-props-no-spreading
                <SignInScreen {...props} />
              )}
            </RootStack.Screen>
            <RootStack.Screen
              name="Восстановление пароля"
              component={PasswordForgetScreen}
            />
            <RootStack.Screen
              name="isSent"
              component={Sent}
              options={{ headerShown: false }}
            />
          </>
        )}
      </RootStack.Navigator>
    </NavigationContainer>
  )
}
